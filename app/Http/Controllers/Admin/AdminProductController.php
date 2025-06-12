<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filtres
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('stock')) {
            if ($request->stock === 'low') {
                $query->whereColumn('stock_quantity', '<=', 'min_stock');
            } elseif ($request->stock === 'out') {
                $query->where('stock_quantity', 0);
            }
        }

        $products = $query->latest()->paginate(15)->appends($request->query());
        $categories = Category::all();

        // Statistiques
        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'low_stock' => Product::whereColumn('stock_quantity', '<=', 'min_stock')->count(),
            'out_of_stock' => Product::where('stock_quantity', 0)->count(),
        ];

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {

    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'unit' => 'nullable|string|max:50',
        'category_id' => 'required|exists:categories,id',
        'origin' => 'nullable|string|max:255',
        'season' => 'nullable|string|max:100',
        'images' => 'nullable|array',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $productData = $request->except('images');
    $productData['slug'] = \Str::slug($request->name);
    $productData['is_active'] = $request->has('is_active');
    $productData['is_organic'] = $request->has('is_organic');
    $productData['is_featured'] = $request->has('is_featured');

    // Upload des images
    if ($request->hasFile('images')) {
        $imagePaths = [];
        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $imagePaths[] = $path;
        }
        $productData['images'] = $imagePaths;
    }

    Product::create($productData);

    return redirect()->route('admin.products.index')
        ->with('success', 'Produit créé avec succès !');
}
    

    public function show(Product $product)
    {
        $product->load(['category']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'origin' => 'nullable|string|max:255',
            'harvest_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:harvest_date',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $productData = $request->except(['new_images', 'remove_images']);
        $productData['is_bio'] = $request->has('is_bio');
        $productData['is_featured'] = $request->has('is_featured');
        $productData['is_active'] = $request->has('is_active');

        // Gestion des nouvelles images
        $currentImages = $product->images ?? [];

        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('products', 'public');
                $currentImages[] = basename($path);
            }
        }

        // Suppression des images sélectionnées
        if ($request->filled('remove_images')) {
            $imagesToRemove = $request->remove_images;
            foreach ($imagesToRemove as $imageToRemove) {
                if (($key = array_search($imageToRemove, $currentImages)) !== false) {
                    unset($currentImages[$key]);
                    Storage::disk('public')->delete('products/' . $imageToRemove);
                }
            }
            $currentImages = array_values($currentImages);
        }

        $productData['images'] = $currentImages;
        $product->update($productData);

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Produit mis à jour avec succès !');
    }

    public function destroy(Product $product)
    {
        // Supprimer les images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete('products/' . $image);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit supprimé avec succès !');
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'activé' : 'désactivé';
        return redirect()->back()
            ->with('success', "Produit {$status} avec succès !");
    }

    public function lowStock()
    {
        $products = Product::whereColumn('stock_quantity', '<=', 'min_stock')
            ->with('category')
            ->orderBy('stock_quantity', 'asc')
            ->get();

        return view('admin.products.low-stock', compact('products'));
    }

    // Actions en lot
    public function bulkActivate(Request $request)
    {
        $ids = json_decode($request->ids);
        Product::whereIn('id', $ids)->update(['is_active' => true]);

        return redirect()->back()->with('success', count($ids) . ' produit(s) activé(s)');
    }

    public function bulkDeactivate(Request $request)
    {
        $ids = json_decode($request->ids);
        Product::whereIn('id', $ids)->update(['is_active' => false]);

        return redirect()->back()->with('success', count($ids) . ' produit(s) désactivé(s)');
    }

    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids);
        $products = Product::whereIn('id', $ids)->get();

        // Supprimer les images
        foreach ($products as $product) {
            if ($product->images) {
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete('products/' . $image);
                }
            }
        }

        Product::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', count($ids) . ' produit(s) supprimé(s)');
    }

    public function updateBulkStock(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.stock_quantity' => 'required|integer|min:0'
        ]);

        foreach ($request->products as $productData) {
            Product::where('id', $productData['id'])
                   ->update(['stock_quantity' => $productData['stock_quantity']]);
        }

        return redirect()->back()->with('success', 'Stock mis à jour pour ' . count($request->products) . ' produit(s)');
    }

    // Export
    public function export()
    {
        $products = Product::with('category')->get();

        $csvData = [];
        $csvData[] = ['ID', 'Nom', 'Description', 'Prix', 'Unité', 'Stock', 'Catégorie', 'Origine', 'Bio', 'Actif'];

        foreach ($products as $product) {
            $csvData[] = [
                $product->id,
                $product->name,
                $product->description,
                $product->price,
                $product->unit,
                $product->stock_quantity,
                $product->category->name ?? '',
                $product->origin ?? '',
                $product->is_bio ? 'Oui' : 'Non',
                $product->is_active ? 'Oui' : 'Non'
            ];
        }

        $filename = 'produits_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        foreach ($csvData as $row) {
            fputcsv($handle, $row, ';');
        }

        fclose($handle);
        exit;
    }
}
