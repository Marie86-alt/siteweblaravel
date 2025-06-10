<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Routing\OrderItem;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
         $orders = Auth::user()->orders()
            ->with('orderItems.product')
            ->recent()
            ->paginate(10);

        return view('orders.index', compact('orders'));//
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */


    /**
     * Display the specified resource.
     */
    public function show(orders $orders)
    {
        $this->authorize('view', $order);

        $order->load(['orderItems.product', 'user']);

        return view('orders.show', compact('order'));//
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $addresses = Auth::user()->addresses()->get();
        $products = Product::whereIn('id', array_keys($cart))->get();

        // Calculer le total
        $subtotal = 0;
        foreach ($cart as $id => $item) {
            $product = $products->find($id);
            if ($product) {
                $subtotal += $product->price * $item['quantity'];
            }
        }

        $shippingCost = $this->calculateShipping($subtotal);
        $taxAmount = $this->calculateTax($subtotal);
        $total = $subtotal + $shippingCost + $taxAmount;

        return view('orders.checkout', compact(
            'cart', 'products', 'addresses', 'subtotal',
            'shippingCost', 'taxAmount', 'total'
        ));//
    }

    /**
     * Update the specified resource in storage.
     */




      public function store(Request $request)
    {
         $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'delivery_date' => 'required|date|after:today',
            'delivery_time_slot' => 'required|in:9h-12h,14h-18h',
            'payment_method' => 'required|in:card,paypal,cash'
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        DB::beginTransaction();

        try {
            // Récupérer les adresses
            $shippingAddress = Address::findOrFail($request->shipping_address_id);
            $billingAddress = Address::findOrFail($request->billing_address_id);

            // Vérifier que les adresses appartiennent à l'utilisateur
            if ($shippingAddress->user_id !== Auth::id() || $billingAddress->user_id !== Auth::id()) {
                throw new \Exception('Adresse non autorisée.');
            }

            // Calculer les montants
            $subtotal = 0;
            $products = Product::whereIn('id', array_keys($cart))->get();

            foreach ($cart as $id => $item) {
                $product = $products->find($id);
                if ($product && $product->stock_quantity >= $item['quantity']) {
                    $subtotal += $product->price * $item['quantity'];
                } else {
                    throw new \Exception("Stock insuffisant pour le produit: {$product->name}");
                }
            }

            $shippingAmount = $this->calculateShipping($subtotal);
            $taxAmount = $this->calculateTax($subtotal);
            $totalAmount = $subtotal + $shippingAmount + $taxAmount;

            // Créer la commande
            $order = Order::create([
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $totalAmount,
                'shipping_address' => $shippingAddress->toArray(),
                'billing_address' => $billingAddress->toArray(),
                'delivery_date' => $request->delivery_date,
                'delivery_time_slot' => $request->delivery_time_slot,
                'delivery_notes' => $request->delivery_notes,
                'payment_method' => $request->payment_method,
                'status' => 'pending'
            ]);

            // Créer les articles de commande et décrémenter le stock
            foreach ($cart as $id => $item) {
                $product = $products->find($id);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'total' => $product->price * $item['quantity']
                ]);

                // Décrémenter le stock
                $product->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();

            // Vider le panier
            session()->forget('cart');

            return redirect()->route('orders.show', $order)
                ->with('success', 'Commande passée avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de la commande: ' . $e->getMessage())
                ->withInput();
        }//
    }


    public function update(Request $request, orders $orders)
    {
        //
    }

     private function calculateShipping($subtotal)
    {
        // Livraison gratuite à partir de 50€
        return $subtotal >= 50 ? 0 : 5.90;
    }
    private function calculateTax($subtotal)
    {
        // Taxe de 5.5% sur le sous-total
        return $subtotal * 0.055;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(orders $orders)
    {
        //
    }
}
