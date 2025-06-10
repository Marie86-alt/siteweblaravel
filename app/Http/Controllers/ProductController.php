<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category'])
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);

        // Filtres
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('bio')) {
            $query->where('is_bio', true);
        }

        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->when($request->min_price, function($q, $min) {
                return $q->where('price', '>=', $min);
            })->when($request->max_price, function($q, $max) {
                return $q->where('price', '<=', $max);
            });
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Tri
        switch ($request->get('sort', 'name')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        $products = $query->paginate(12)->appends($request->query());
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        // Produits similaires
        $relatedProducts = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function featured()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where('is_featured', true)
            ->paginate(12);

        return view('products.featured', compact('products'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return redirect()->route('products.index');
        }

        $products = Product::with('category')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%');
            })
            ->paginate(12)
            ->appends(['q' => $query]);

        return view('products.search', compact('products', 'query'));
    }

    // API Methods
    public function apiSearch(Request $request)
    {
        $query = $request->get('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where('name', 'like', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'name', 'price', 'slug']);

        return response()->json($products);
    }
}
