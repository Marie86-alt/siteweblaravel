<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageGenerationService;
use App\Models\Product;
use App\Models\Category;  // AJOUTÉ
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImageGenerationController extends Controller
{
    private $imageService;

    public function __construct(ImageGenerationService $imageService)
    {
        $this->middleware(['auth', 'admin']);
        $this->imageService = $imageService;
    }

    /**
     * Afficher l'interface de génération d'images
     */
    public function index()
    {
        // Statistiques Produits
        $totalProducts = Product::count();
        $productsWithImages = Product::whereNotNull('images')
            ->where('images', '!=', '[]')
            ->where('images', '!=', '')
            ->count();
        $productsWithoutImages = $totalProducts - $productsWithImages;

        // Statistiques Catégories (AJOUTÉ)
        $totalCategories = Category::count();
        $categoriesWithImages = Category::whereNotNull('image')
            ->where('image', '!=', '')
            ->count();
        $categoriesWithoutImages = $totalCategories - $categoriesWithImages;

        // Récupérer tous les produits et catégories pour l'affichage
        $products = Product::with('category')->orderBy('name')->get();
        $categories = Category::withCount('products')->orderBy('name')->get(); // AJOUTÉ

        return view('admin.image-generation.index', compact(
            'totalProducts',
            'productsWithImages',
            'productsWithoutImages',
            'totalCategories',        // AJOUTÉ
            'categoriesWithImages',   // AJOUTÉ
            'categoriesWithoutImages', // AJOUTÉ
            'products',
            'categories'              // AJOUTÉ
        ));
    }

    /**
     * Générer une image pour un produit spécifique
     */
    public function generateSingle(Request $request, Product $product)
    {
        try {
            $imagePath = $this->imageService->generateProductImage($product);

            if ($imagePath) {
                return response()->json([
                    'success' => true,
                    'message' => 'Image générée avec succès !',
                    'image_path' => $imagePath,
                    'image_url' => asset('storage/products/' . $imagePath)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de la génération d\'image'
                ], 422);
            }

        } catch (\Exception $e) {
            Log::error('Erreur génération image: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer des images en lot
     */
    public function generateBatch(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:50',
            'force' => 'boolean'
        ]);

        try {
            $limit = $request->get('limit', 10);
            $force = $request->get('force', false);

            // Récupérer les produits à traiter
            $query = Product::query();

            if (!$force) {
                $query->where(function($q) {
                    $q->whereNull('images')
                      ->orWhere('images', '[]')
                      ->orWhere('images', '');
                });
            }

            $products = $query->limit($limit)->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Aucun produit à traiter',
                    'results' => []
                ]);
            }

            // Lancer la génération
            $results = [];
            foreach ($products as $product) {
                $imagePath = $this->imageService->generateProductImage($product);

                $results[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'success' => $imagePath ? true : false,
                    'image_path' => $imagePath
                ];

                // Pause pour éviter la surcharge
                if (count($results) < $products->count()) {
                    sleep(1);
                }
            }

            $successCount = collect($results)->where('success', true)->count();

            return response()->json([
                'success' => true,
                'message' => "{$successCount} image(s) générée(s) sur {$products->count()}",
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur génération batch: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Régénérer une image existante
     */
    public function regenerate(Request $request, Product $product)
    {
        try {
            $imagePath = $this->imageService->regenerateProductImage($product);

            if ($imagePath) {
                return response()->json([
                    'success' => true,
                    'message' => 'Image régénérée avec succès !',
                    'image_path' => $imagePath,
                    'image_url' => asset('storage/products/' . $imagePath)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de la régénération'
                ], 422);
            }

        } catch (\Exception $e) {
            Log::error('Erreur régénération image: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer une image pour une catégorie spécifique
     */
    public function generateCategorySingle(Request $request, Category $category)
    {
        try {
            $result = $this->imageService->generateCategoryImage($category);

            if ($result && isset($result['success']) && $result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Image de catégorie générée avec succès !',
                    'image_path' => $result['image_path'],
                    'image_url' => asset('storage/' . $result['image_path']),
                    'prompt_used' => $result['prompt_used'] ?? null
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Échec de la génération d\'image'
                ], 422);
            }

        } catch (\Exception $e) {
            Log::error('Erreur génération image catégorie: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer des images en lot pour les catégories
     */
    public function generateCategoryBatch(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:20',
            'force' => 'boolean',
            'style' => 'string|in:professional,artistic,minimal,vibrant'
        ]);

        try {
            $limit = $request->get('limit', 10);
            $force = $request->get('force', false);
            $style = $request->get('style', 'professional');

            // Récupérer les catégories à traiter
            $query = Category::query();

            if (!$force) {
                $query->where(function($q) {
                    $q->whereNull('image')
                      ->orWhere('image', '');
                });
            }

            $categories = $query->limit($limit)->get();

            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Aucune catégorie à traiter',
                    'results' => []
                ]);
            }

            // Lancer la génération
            $results = [];
            foreach ($categories as $category) {
                $result = $this->imageService->generateCategoryImage($category, $style);

                $results[] = [
                    'category_id' => $category->id,
                    'category_name' => $category->name,
                    'success' => $result['success'] ?? false,
                    'image_path' => $result['image_path'] ?? null,
                    'error' => $result['error'] ?? null
                ];

                // Pause pour éviter la surcharge
                if (count($results) < $categories->count()) {
                    sleep(2);
                }
            }

            $successCount = collect($results)->where('success', true)->count();

            return response()->json([
                'success' => true,
                'message' => "{$successCount} image(s) de catégorie générée(s) sur {$categories->count()}",
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur génération batch catégories: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Régénérer une image existante pour une catégorie
     */
    public function regenerateCategory(Request $request, Category $category)
    {
        try {
            $style = $request->get('style', 'professional');
            $result = $this->imageService->generateCategoryImage($category, $style);

            if ($result && isset($result['success']) && $result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Image de catégorie régénérée avec succès !',
                    'image_path' => $result['image_path'],
                    'image_url' => asset('storage/' . $result['image_path']),
                    'prompt_used' => $result['prompt_used'] ?? null
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Échec de la régénération'
                ], 422);
            }

        } catch (\Exception $e) {
            Log::error('Erreur régénération image catégorie: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer l'image d'une catégorie
     */
    public function deleteCategoryImage(Request $request, Category $category)
    {
        try {
            $deleted = $this->imageService->deleteCategoryImage($category);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Image supprimée avec succès !'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune image à supprimer'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Erreur suppression image catégorie: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ], 500);
        }
    }
}
