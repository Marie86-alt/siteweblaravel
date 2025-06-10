<?php

namespace App\Services;

use App\Models\Product;
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
    private function generateImageFromDescription(string $description, Product $product): ?string
    {
        try {
            if (!$this->openaiKey) {
                Log::warning('Clé API OpenAI manquante');
                return $this->generatePlaceholderImage($product);
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
            return $this->generatePlaceholderImage($product);

        } catch (\Exception $e) {
            Log::error('Exception génération image: ' . $e->getMessage());
            return $this->generatePlaceholderImage($product);
        }
    }

    /**
     * Générer une image placeholder si l'API échoue
     */
    private function generatePlaceholderImage(Product $product): string
    {
        $productName = urlencode($product->name);
        return "https://via.placeholder.com/400x400/27ae60/ffffff?text={$productName}";
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
}
