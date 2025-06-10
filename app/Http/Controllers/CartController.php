<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if ($product && $product->is_active) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product->price * $item['quantity']
                ];
                $total += $product->price * $item['quantity'];
            }
        }

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if (!$product->is_active || $product->stock_quantity <= 0) {
            return redirect()->back()->with('error', 'Ce produit n\'est plus disponible.');
        }

        $cart = session()->get('cart', []);
        $quantity = $request->quantity;

        if (isset($cart[$product->id])) {
            $newQuantity = $cart[$product->id]['quantity'] + $quantity;

            if ($newQuantity > $product->stock_quantity) {
                return redirect()->back()->with('error', 'Stock insuffisant.');
            }

            $cart[$product->id]['quantity'] = $newQuantity;
        } else {
            if ($quantity > $product->stock_quantity) {
                return redirect()->back()->with('error', 'Stock insuffisant.');
            }

            $cart[$product->id] = [
                'name' => $product->name,
                'quantity' => $quantity,
                'price' => $product->price,
                'image' => $product->main_image ?? null,
                'unit' => $product->unit,
                'slug' => $product->slug
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Produit ajouté au panier !');
    }

    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $product = Product::find($productId);

            if (!$product || $request->quantity > $product->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuffisant ou produit non trouvé.'
                ], 400);
            }

            $cart[$productId]['quantity'] = $request->quantity;
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Quantité mise à jour.',
                'cart_count' => array_sum(array_column($cart, 'quantity'))
            ]);
        }

        return  response()->json([
            'success' => false,
            'message' => 'Produit non trouvé dans le panier.'
        ], 404);
    }

    public function remove($productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Produit retiré du panier.');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Panier vidé.');
    }

    public function count()
    {
        $cart = session()->get('cart', []);
        $count = array_sum(array_column($cart, 'quantity'));

        return response()->json(['count' => $count]);
    }

    // API Methods
    public function apiAdd(Request $request, Product $product)
    {
        // Logique similaire à add() mais retourne du JSON
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if (!$product->is_active || $product->stock_quantity <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ce produit n\'est plus disponible.'
            ], 400);
        }

        $cart = session()->get('cart', []);
        $quantity = $request->quantity;

        if (isset($cart[$product->id])) {
            $newQuantity = $cart[$product->id]['quantity'] + $quantity;

            if ($newQuantity > $product->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuffisant.'
                ], 400);
            }

            $cart[$product->id]['quantity'] = $newQuantity;
        } else {
            if ($quantity > $product->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuffisant.'
                ], 400);
            }

            $cart[$product->id] = [
                'name' => $product->name,
                'quantity' => $quantity,
                'price' => $product->price,
                'image' => $product->main_image ?? null,
                'unit' => $product->unit,
                'slug' => $product->slug
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté au panier !',
            'cart_count' => array_sum(array_column($cart, 'quantity'))
        ]);
    }
}
