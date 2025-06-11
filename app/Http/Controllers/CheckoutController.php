<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Log::info('CheckoutController initialisé');
    }

    /**
     * Afficher la page de checkout
     */
    public function index()
    {
        Log::info('Début de CheckoutController::index');
        $user = Auth::user();
        Log::info('Utilisateur authentifié', ['user_id' => $user->id]);

        // ✅ CORRIGÉ : Récupérer les articles du panier depuis la session
        $cart = session()->get('cart', []);
        Log::info('Panier récupéré de la session', ['cart' => $cart]);
        $cartItems = [];

        // Transformer les données de session en format similaire à celui attendu par la vue
        foreach ($cart as $id => $item) {
            $product = Product::with('category')->find($id);
            if ($product && $product->is_active) {
                $cartItems[] = (object)[
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product->price * $item['quantity']
                ];
            }
        }
        Log::info('Articles du panier transformés', ['cartItems_count' => count($cartItems)]);

        if (empty($cartItems)) {
            Log::warning('Panier vide, redirection vers cart.index');
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide. Ajoutez des produits avant de commander.');
        }

        // Vérifier la disponibilité des stocks
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock_quantity) {
                Log::warning('Stock insuffisant', [
                    'product' => $item->product->name,
                    'quantity' => $item->quantity,
                    'stock_available' => $item->product->stock_quantity
                ]);
                return redirect()->route('cart.index')
                    ->with('error', "Stock insuffisant pour {$item->product->name}. Stock disponible: {$item->product->stock_quantity}");
            }
        }

        // Calculer les totaux
        $subtotal = array_sum(array_map(function($item) {
            return $item->quantity * $item->product->price;
        }, $cartItems));
        $shipping = $subtotal >= 50 ? 0 : 5.90;
        $total = $subtotal + $shipping;
        Log::info('Totaux calculés', [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total
        ]);

        // Récupérer les adresses de l'utilisateur (si vous avez une table addresses)
        $addresses = collect(); // Vide pour l'instant
        Log::info('Adresses récupérées', ['addresses_count' => $addresses->count()]);

        return view('checkout.index', compact(
            'cartItems',
            'subtotal',
            'shipping',
            'total',
            'addresses',
            'user'
        ));
    }

    /**
     * Traiter la commande
     */
    public function store(Request $request)
    {
        Log::info('Début de CheckoutController::store', $request->all());
        $user = Auth::user();
        Log::info('Utilisateur authentifié', ['user_id' => $user->id]);

        // Validation des données
        $validated = $request->validate([
            'billing_first_name' => 'required|string|max:255',
            'billing_last_name' => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone' => 'required|string|max:20',
            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:255',
            'billing_postal_code' => 'required|string|max:10',
            'billing_country' => 'required|string|max:255',
            'delivery_same_as_billing' => 'boolean',
            'delivery_first_name' => 'required_if:delivery_same_as_billing,false|string|max:255|nullable',
            'delivery_last_name' => 'required_if:delivery_same_as_billing,false|string|max:255|nullable',
            'delivery_address' => 'required_if:delivery_same_as_billing,false|string|max:255|nullable',
            'delivery_city' => 'required_if:delivery_same_as_billing,false|string|max:255|nullable',
            'delivery_postal_code' => 'required_if:delivery_same_as_billing,false|string|max:10|nullable',
            'delivery_country' => 'required_if:delivery_same_as_billing,false|string|max:255|nullable',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:card,transfer,cash',
        ]);
        Log::info('Validation réussie', $validated);

        // ✅ CORRIGÉ : Récupérer les articles du panier depuis la session
        $cart = session()->get('cart', []);
        Log::info('Panier récupéré', ['cart' => $cart]);
        $cartItems = [];

        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if ($product && $product->is_active) {
                $cartItems[] = (object)[
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product->price * $item['quantity']
                ];
            }
        }
        Log::info('Articles du panier transformés', ['cartItems_count' => count($cartItems)]);

        if (empty($cartItems)) {
            Log::warning('Panier vide, redirection vers cart.index');
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        DB::beginTransaction();
        Log::info('Début de la transaction');

        try {
            // Calculer les totaux
            $subtotal = array_sum(array_map(function($item) {
                return $item->quantity * $item->product->price;
            }, $cartItems));
            $shipping = $subtotal >= 50 ? 0 : 5.90;
            $total = $subtotal + $shipping;
            Log::info('Totaux calculés', [
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total
            ]);

            // ✅ CONSTRUIRE LES ADRESSES CORRECTEMENT
            $billingAddress = $validated['billing_first_name'] . ' ' . $validated['billing_last_name'] . "\n" .
                            $validated['billing_address'] . "\n" .
                            $validated['billing_postal_code'] . ' ' . $validated['billing_city'] . "\n" .
                            $validated['billing_country'];

            // Gérer l'adresse de livraison selon la checkbox
            $shippingAddress = $billingAddress; // Par défaut, même adresse
            if (!$validated['delivery_same_as_billing']) {
                $shippingAddress = $validated['delivery_first_name'] . ' ' . $validated['delivery_last_name'] . "\n" .
                                 $validated['delivery_address'] . "\n" .
                                 $validated['delivery_postal_code'] . ' ' . $validated['delivery_city'] . "\n" .
                                 $validated['delivery_country'];
            }

            // ✅ CRÉER LA COMMANDE AVEC TOUS LES CHAMPS REQUIS
            $orderNumber = $this->generateOrderNumber();
            Log::info('Numéro de commande généré', ['order_number' => $orderNumber]);

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_amount' => $shipping,
                'total_amount' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'notes' => $validated['notes'],
                'billing_address' => $billingAddress,
                'shipping_address' => $shippingAddress,  // ✅ AJOUTÉ CORRECTEMENT
                // ✅ AJOUT DES CHAMPS INDIVIDUELS SI VOTRE MIGRATION LES CONTIENT
                'billing_first_name' => $validated['billing_first_name'],
                'billing_last_name' => $validated['billing_last_name'],
                'billing_email' => $validated['billing_email'],
                'billing_phone' => $validated['billing_phone'],
                'billing_city' => $validated['billing_city'],
                'billing_postal_code' => $validated['billing_postal_code'],
                'billing_country' => $validated['billing_country'],
                'delivery_first_name' => $validated['delivery_same_as_billing'] ? $validated['billing_first_name'] : $validated['delivery_first_name'],
                'delivery_last_name' => $validated['delivery_same_as_billing'] ? $validated['billing_last_name'] : $validated['delivery_last_name'],
                'delivery_address' => $validated['delivery_same_as_billing'] ? $validated['billing_address'] : $validated['delivery_address'],
                'delivery_city' => $validated['delivery_same_as_billing'] ? $validated['billing_city'] : $validated['delivery_city'],
                'delivery_postal_code' => $validated['delivery_same_as_billing'] ? $validated['billing_postal_code'] : $validated['delivery_postal_code'],
                'delivery_country' => $validated['delivery_same_as_billing'] ? $validated['billing_country'] : $validated['delivery_country'],
            ]);
            Log::info('Commande créée', ['order_id' => $order->id]);

            // Créer les items de commande et mettre à jour les stocks
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                Log::info('Traitement item', ['product_id' => $product->id, 'quantity' => $cartItem->quantity]);

                // Vérifier le stock encore une fois
                if ($cartItem->quantity > $product->stock_quantity) {
                    Log::warning('Stock insuffisant', [
                        'product' => $product->name,
                        'quantity' => $cartItem->quantity,
                        'stock_available' => $product->stock_quantity
                    ]);
                    throw new \Exception("Stock insuffisant pour {$product->name}");
                }

                // Créer l'item de commande
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'price' => $product->price,
                    'quantity' => $cartItem->quantity,
                    'total' => $cartItem->quantity * $product->price,
                ]);
                Log::info('Item de commande créé', ['product_id' => $product->id]);

                // Mettre à jour le stock
                $product->decrement('stock_quantity', $cartItem->quantity);
                Log::info('Stock mis à jour', ['product_id' => $product->id, 'new_stock' => $product->stock_quantity]);
            }

            // ✅ CORRIGÉ : Vider le panier de la session
            session()->forget('cart');
            Log::info('Panier vidé');

            DB::commit();
            Log::info('Transaction validée');
            \App\Http\Controllers\CustomerController::sendOrderConfirmation($order);


            // Rediriger vers la page de confirmation
            Log::info('Redirection vers checkout.confirmation', ['order_id' => $order->id]);
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('success', 'Votre commande a été créée avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur dans CheckoutController::store', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la commande : ' . $e->getMessage());
        }
    }

    /**
     * Page de confirmation de commande
     */
    public function confirmation($orderId)
    {
        Log::info('Début de CheckoutController::confirmation', ['order_id' => $orderId]);
        $order = Order::with(['orderItems.product', 'user'])
            ->where('id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            Log::warning('Commande introuvable', ['order_id' => $orderId, 'user_id' => Auth::id()]);
            return redirect()->route('home')
                ->with('error', 'Commande introuvable.');
        }

        Log::info('Commande récupérée, affichage de la vue', ['order_id' => $order->id]);
        return view('checkout.confirmation', compact('order'));
    }

    /**
     * Générer un numéro de commande unique
     */
    private function generateOrderNumber()
    {
        do {
            $orderNumber = 'FL-' . date('Y') . '-' . Str::upper(Str::random(6));
        } while (Order::where('order_number', $orderNumber)->exists());

        Log::info('Numéro de commande généré', ['order_number' => $orderNumber]);
        return $orderNumber;
    }
}
