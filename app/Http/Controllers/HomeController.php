<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
     public function index()
    {
        // Produits en vedette
        $featuredProducts = Product::with('category')
            ->where('is_featured', true)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->limit(8)
            ->get();

        // Si pas assez de produits en vedette, prendre les plus récents
        if ($featuredProducts->count() < 6) {
            $featuredProducts = Product::with('category')
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->latest()
                ->limit(8)
                ->get();
        }

        // Catégories principales
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        // Produits bio
        $bioProducts = Product::with('category')
            ->where('is_bio', true)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->limit(4)
            ->get();

        return view('home', compact('featuredProducts', 'categories', 'bioProducts'));
    }
}

