<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;  // AJOUTÉ
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageGenerationService
{
    private $apiKey;
    private $openaiKey;
    private $baseUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key');
        $this->openaiKey = config('services.openai.api_key');
    }

    // ==================== MÉTHODES PRODUITS (Existantes) ====================

    /**
     * Générer une image pour un produit
     */
    public function generateProductImage(Product $product): ?string
    {
        try {
            Log::info("Génération d'image pour le produit: {$product->name}");

            // Préparer le prompt pour Claude
            $prompt = $this->buildImagePrompt($product);

            // Appeler l'API Claude pour obtenir une description détaillée
            $imageDescription = $this->getImageDescriptionFromClaude($prompt);

            if (!$imageDescription) {
                Log::error("Impossible d'obtenir une description d'image pour {$product->name}");
                return null;
            }

            // Générer l'image via DALL-E
            $imageUrl = $this->generateImageFromDescription($imageDescription, $product);

            if ($imageUrl) {
                // Télécharger et sauvegarder l'image localement
                return $this->downloadAndSaveImage($imageUrl, $product);
            }

            return null;

        } catch (\Exception $e) {
            Log::error("Erreur lors de la génération d'image pour {$product->name}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Construire le prompt pour Claude
     */
    private function buildImagePrompt(Product $product): string
    {
        $category = $product->category ? $product->category->name : 'produit alimentaire';

        return "Je vends des fruits et légumes bio en ligne. J'ai besoin d'une description détaillée pour générer une image du produit suivant :

Nom: {$product->name}
Catégorie: {$category}
Description: {$product->description}
Prix: {$product->price}€ / {$product->unit}

Peux-tu me donner une description détaillée et professionnelle pour générer une image marketing de ce produit ?
L'image doit être :
- Fond blanc ou neutre
- Éclairage naturel
- Style épuré et professionnel
- Parfait pour un site e-commerce
- Mettant en valeur la fraîcheur du produit

Réponds uniquement avec la description de l'image, pas d'explication.";
    }

    /**
     * Obtenir une description d'image via Claude
     */
    private function getImageDescriptionFromClaude(string $prompt): ?string
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'content-type' => 'application/json',
                'anthropic-version' => '2023-06-01'
            ])->post($this->baseUrl, [
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 300,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['content'][0]['text'] ?? null;
            }

            Log::error('Erreur API Claude: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Exception API Claude: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Générer l'image à partir de la description via DALL-E
     */
    private function generateImageFromDescription(string $description, $item): ?string
    {
        try {
            if (!$this->openaiKey) {
                Log::warning('Clé API OpenAI manquante');
                return $this->generatePlaceholderImage($item);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openaiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.openai.com/v1/images/generations', [
                'model' => 'dall-e-3',
                'prompt' => $description,
                'n' => 1,
                'size' => '1024x1024',
                'quality' => 'standard',
                'style' => 'natural'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'][0]['url'] ?? null;
            }

            Log::error('Erreur API OpenAI: ' . $response->body());
            return $this->generatePlaceholderImage($item);

        } catch (\Exception $e) {
            Log::error('Exception génération image: ' . $e->getMessage());
            return $this->generatePlaceholderImage($item);
        }
    }

    /**
     * Générer une image placeholder si l'API échoue
     */
    private function generatePlaceholderImage($item): string
    {
        $name = $item->name ?? 'Produit';
        $encodedName = urlencode($name);
        return "https://via.placeholder.com/400x400/27ae60/ffffff?text={$encodedName}";
    }

    /**
     * Télécharger et sauvegarder l'image localement
     */
    private function downloadAndSaveImage(string $imageUrl, Product $product): ?string
    {
        try {
            $imageContent = Http::get($imageUrl)->body();

            if (empty($imageContent)) {
                return null;
            }

            // Générer un nom de fichier unique
            $fileName = 'generated_' . Str::slug($product->name) . '_' . time() . '.jpg';
            $path = 'products/' . $fileName;

            // Sauvegarder dans le storage
            Storage::disk('public')->put($path, $imageContent);

            // Mettre à jour le produit avec la nouvelle image
            $images = $product->images ?? [];
            array_unshift($images, $fileName); // Ajouter en première position
            $product->update(['images' => $images]);

            Log::info("Image générée et sauvegardée: {$fileName} pour {$product->name}");

            return $fileName;

        } catch (\Exception $e) {
            Log::error("Erreur lors de la sauvegarde d'image: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Générer des images pour tous les produits sans image
     */
    public function generateMissingImages(): array
    {
        $results = [];

        $productsWithoutImages = Product::where(function($query) {
            $query->whereNull('images')
                  ->orWhere('images', '[]')
                  ->orWhere('images', '');
        })->get();

        Log::info("Génération d'images pour {$productsWithoutImages->count()} produits");

        foreach ($productsWithoutImages as $product) {
            $imagePath = $this->generateProductImage($product);

            $results[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'image_generated' => $imagePath ? true : false,
                'image_path' => $imagePath
            ];

            // Pause pour éviter de surcharger les APIs
            sleep(2);
        }

        return $results;
    }

    /**
     * Régénérer l'image d'un produit spécifique
     */
    public function regenerateProductImage(Product $product): ?string
    {
        Log::info("Régénération d'image pour le produit: {$product->name}");

        // Supprimer les anciennes images générées
        if ($product->images) {
            foreach ($product->images as $image) {
                if (Str::startsWith($image, 'generated_')) {
                    Storage::disk('public')->delete('products/' . $image);
                }
            }
        }

        return $this->generateProductImage($product);
    }

    // ==================== NOUVELLES MÉTHODES CATÉGORIES ====================

    /**
     * Générer une image pour une catégorie
     */
    public function generateCategoryImage(Category $category, string $style = 'professional'): array
    {
        try {
            Log::info("Génération d'image pour la catégorie: {$category->name}");

            // Construire le prompt pour la catégorie
            $prompt = $this->buildCategoryPrompt($category, $style);

            // Obtenir la description via Claude
            $imageDescription = $this->getCategoryDescriptionFromClaude($prompt, $category, $style);

            if (!$imageDescription) {
                return [
                    'success' => false,
                    'error' => "Impossible d'obtenir une description d'image pour {$category->name}"
                ];
            }

            // Générer l'image via DALL-E
            $imageUrl = $this->generateImageFromDescription($imageDescription, $category);

            if ($imageUrl) {
                // Télécharger et sauvegarder l'image
                $savedPath = $this->downloadAndSaveCategoryImage($imageUrl, $category, $style);

                if ($savedPath) {
                    return [
                        'success' => true,
                        'image_path' => $savedPath,
                        'prompt_used' => $imageDescription
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Échec de la génération ou sauvegarde de l\'image'
            ];

        } catch (\Exception $e) {
            Log::error("Erreur génération image catégorie {$category->name}: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Construire le prompt pour une catégorie
     */
    private function buildCategoryPrompt(Category $category, string $style): string
    {
        $styleDescriptions = [
            'professional' => 'style professionnel et commercial, éclairage studio, fond blanc',
            'artistic' => 'style artistique et créatif, composition esthétique, éclairage dramatique',
            'minimal' => 'style minimaliste et épuré, composition simple, couleurs neutres',
            'vibrant' => 'style vibrant et coloré, composition dynamique, couleurs vives'
        ];

        $styleDesc = $styleDescriptions[$style] ?? $styleDescriptions['professional'];

        return "Je vends des fruits et légumes bio en ligne. J'ai besoin d'une description pour générer une image d'illustration de catégorie :

Catégorie: {$category->name}
Description: {$category->description}
Style souhaité: {$styleDesc}

Crée une description détaillée pour DALL-E qui génère une image représentative de cette catégorie de produits.
L'image doit être :
- Représentative de la catégorie {$category->name}
- Attrayante visuellement
- Adaptée pour un site e-commerce
- En {$styleDesc}
- Sans texte visible dans l'image

Réponds uniquement avec la description DALL-E en anglais, maximum 100 mots.";
    }

    /**
     * Obtenir une description d'image pour catégorie via Claude
     */
    private function getCategoryDescriptionFromClaude(string $prompt, Category $category, string $style): ?string
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'content-type' => 'application/json',
                'anthropic-version' => '2023-06-01'
            ])->timeout(30)->post($this->baseUrl, [
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 200,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['content'][0]['text'] ?? null;
            }

            Log::error('Erreur API Claude pour catégorie: ' . $response->body());
            return $this->buildFallbackCategoryPrompt($category, $style);

        } catch (\Exception $e) {
            Log::error('Exception API Claude pour catégorie: ' . $e->getMessage());
            return $this->buildFallbackCategoryPrompt($category, $style);
        }
    }

    /**
     * Prompt de secours pour les catégories
     */
    private function buildFallbackCategoryPrompt(Category $category, string $style): string
    {
        $prompts = [
            'Fruits' => 'Fresh colorful fruits arrangement, natural lighting, professional photography',
            'Légumes' => 'Fresh vegetables display, organic produce, clean background',
            'Herbes aromatiques' => 'Fresh herbs and aromatic plants, natural arrangement',
            'Produits bio' => 'Organic fresh produce, eco-friendly display, natural setting'
        ];

        $basePrompt = $prompts[$category->name] ?? "Fresh {$category->name} products, professional photography";

        return $basePrompt . ', high quality, commercial style, ' . $style . ' aesthetic';
    }

    /**
     * Télécharger et sauvegarder l'image de catégorie
     */
    private function downloadAndSaveCategoryImage(string $imageUrl, Category $category, string $style): ?string
    {
        try {
            $imageContent = Http::timeout(30)->get($imageUrl)->body();

            if (empty($imageContent)) {
                return null;
            }

            // Générer un nom de fichier unique
            $fileName = 'category_' . Str::slug($category->name) . '_' . $style . '_' . time() . '.jpg';
            $path = 'categories/' . $fileName;

            // Sauvegarder dans le storage
            Storage::disk('public')->put($path, $imageContent);

            // Mettre à jour la catégorie
            $category->update([
                'image' => $path,
                'ai_generated' => true,
                'ai_prompt' => "Style: {$style}",
                'image_generated_at' => now()
            ]);

            Log::info("Image catégorie générée: {$fileName} pour {$category->name}");

            return $path;

        } catch (\Exception $e) {
            Log::error("Erreur sauvegarde image catégorie: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Supprimer l'image d'une catégorie
     */
    public function deleteCategoryImage(Category $category): bool
    {
        try {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);

                $category->update([
                    'image' => null,
                    'ai_generated' => false,
                    'ai_prompt' => null,
                    'image_generated_at' => null
                ]);

                Log::info("Image supprimée pour catégorie: {$category->name}");
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Erreur suppression image catégorie: " . $e->getMessage());
            return false;
        }
    }
}
