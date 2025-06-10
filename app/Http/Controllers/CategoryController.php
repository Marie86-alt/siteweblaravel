<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Afficher toutes les catégories
     */
    public function index(Request $request)
    {
        try {
            Log::info('Début CategoryController::index', $request->all());

            // Récupérer toutes les catégories actives avec le nombre de produits
            $categories = Category::where('is_active', true)
                ->withCount(['products' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->get();

            Log::info('Catégories récupérées', ['count' => $categories->count()]);

            // Statistiques pour la page
            $stats = [
                'total_categories' => $categories->count(),
                'total_products' => Product::where('is_active', true)->count(),
                'bio_products' => Product::where('is_active', true)->where('is_bio', true)->count(),
                'featured_categories' => $categories->where('is_featured', true)->count(),
            ];

            Log::info('Statistiques calculées', $stats);

            return view('categories.index', compact('categories', 'stats'));

        } catch (\Exception $e) {
            Log::error('Erreur dans CategoryController::index', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // En cas d'erreur, afficher la page avec des données vides
            return view('categories.index', [
                'categories' => collect(),
                'stats' => [
                    'total_categories' => 0,
                    'total_products' => 0,
                    'bio_products' => 0,
                    'featured_categories' => 0,
                ]
            ]);
        }
    }

    /**
     * Afficher une catégorie spécifique avec ses produits
     */
    public function show(Category $category, Request $request)
    {
        try {
            Log::info('Début CategoryController::show', [
                'category_id' => $category->id,
                'category_name' => $category->name
            ]);

            // Vérifier que la catégorie est active
            if (!$category->is_active) {
                Log::warning('Tentative d\'accès à une catégorie inactive', ['category_id' => $category->id]);
                abort(404);
            }

            // Query des produits de cette catégorie
            $productsQuery = $category->products()
                ->where('is_active', true)
                ->with('category');

            // Filtres optionnels
            if ($request->filled('bio')) {
                $productsQuery->where('is_bio', true);
                Log::info('Filtre bio appliqué');
            }

            if ($request->filled('featured')) {
                $productsQuery->where('is_featured', true);
                Log::info('Filtre vedette appliqué');
            }

            if ($request->filled('price_min')) {
                $productsQuery->where('price', '>=', $request->price_min);
                Log::info('Filtre prix minimum appliqué', ['price_min' => $request->price_min]);
            }

            if ($request->filled('price_max')) {
                $productsQuery->where('price', '<=', $request->price_max);
                Log::info('Filtre prix maximum appliqué', ['price_max' => $request->price_max]);
            }

            // Tri
            $sortBy = $request->get('sort', 'name');
            $sortOrder = $request->get('order', 'asc');

            switch ($sortBy) {
                case 'price':
                    $productsQuery->orderBy('price', $sortOrder);
                    break;
                case 'created_at':
                    $productsQuery->orderBy('created_at', 'desc');
                    break;
                case 'featured':
                    $productsQuery->orderBy('is_featured', 'desc')
                                  ->orderBy('name', 'asc');
                    break;
                default:
                    $productsQuery->orderBy('name', $sortOrder);
            }

            // Pagination
            $products = $productsQuery->paginate(12)->appends($request->query());

            Log::info('Produits récupérés', [
                'count' => $products->count(),
                'total' => $products->total()
            ]);

            // Statistiques de la catégorie
            $categoryStats = [
                'total_products' => $category->products()->where('is_active', true)->count(),
                'bio_products' => $category->products()->where('is_active', true)->where('is_bio', true)->count(),
                'featured_products' => $category->products()->where('is_active', true)->where('is_featured', true)->count(),
                'price_range' => $category->products()->where('is_active', true)->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first(),
            ];

            // Catégories similaires
            $relatedCategories = Category::where('is_active', true)
                ->where('id', '!=', $category->id)
                ->withCount(['products' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->orderBy('products_count', 'desc')
                ->take(4)
                ->get();

            return view('categories.show', compact(
                'category',
                'products',
                'categoryStats',
                'relatedCategories'
            ));

        } catch (\Exception $e) {
            Log::error('Erreur dans CategoryController::show', [
                'category_id' => $category->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('categories.index')
                ->with('error', 'Erreur lors du chargement de la catégorie.');
        }
    }

    /**
     * API pour filtrer les catégories (AJAX)
     */
    public function filter(Request $request)
    {
        try {
            $query = Category::where('is_active', true)
                ->withCount(['products' => function ($q) {
                    $q->where('is_active', true);
                }]);

            // Filtres
            if ($request->filled('type')) {
                switch ($request->type) {
                    case 'bio':
                        $query->whereHas('products', function ($q) {
                            $q->where('is_bio', true)->where('is_active', true);
                        });
                        break;
                    case 'featured':
                        $query->where('is_featured', true);
                        break;
                    case 'seasonal':
                        // Logique pour les produits saisonniers
                        $query->where('is_seasonal', true);
                        break;
                }
            }

            // Tri
            $sortBy = $request->get('sort', 'name');
            switch ($sortBy) {
                case 'popularity':
                    $query->orderBy('products_count', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('name', 'asc');
            }

            $categories = $query->get();

            return response()->json([
                'success' => true,
                'categories' => $categories,
                'count' => $categories->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur dans CategoryController::filter', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du filtrage'
            ], 500);
        }
    }

    /**
     * Recherche dans les catégories (AJAX)
     */
    public function search(Request $request)
    {
        try {
            $searchTerm = $request->get('q', '');

            if (strlen($searchTerm) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le terme de recherche doit contenir au moins 2 caractères'
                ]);
            }

            $categories = Category::where('is_active', true)
                ->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%{$searchTerm}%")
                          ->orWhere('description', 'like', "%{$searchTerm}%");
                })
                ->withCount(['products' => function ($q) {
                    $q->where('is_active', true);
                }])
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'categories' => $categories,
                'count' => $categories->count(),
                'search_term' => $searchTerm
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur dans CategoryController::search', [
                'message' => $e->getMessage(),
                'search_term' => $request->get('q', '')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche'
            ], 500);
        }
    }
}
