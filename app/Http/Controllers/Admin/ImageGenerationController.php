<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageGenerationService;
use App\Models\Product;
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
        // Statistiques
        $totalProducts = Product::count();
        $productsWithImages = Product::whereNotNull('images')
            ->where('images', '!=', '[]')
            ->where('images', '!=', '')
            ->count();
        $productsWithoutImages = $totalProducts - $productsWithImages;

        // Récupérer tous les produits pour l'affichage
        $products = Product::with('category')->orderBy('name')->get();

        return view('admin.image-generation.index', compact(
            'totalProducts',
            'productsWithImages',
            'productsWithoutImages',
            'products'
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
}
