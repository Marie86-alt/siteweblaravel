<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageGenerationService;
use App\Models\Product;

class GenerateProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:generate-images
                           {--product= : ID du produit spécifique à traiter}
                           {--force : Régénérer même si des images existent}
                           {--limit=10 : Nombre maximum de produits à traiter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Générer des images pour les produits via l\'IA';

    private $imageService;

    public function __construct(ImageGenerationService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎨 Génération d\'images de produits via IA');
        $this->newLine();

        // Si un produit spécifique est demandé
        if ($productId = $this->option('product')) {
            return $this->handleSingleProduct($productId);
        }

        // Sinon, traiter plusieurs produits
        return $this->handleMultipleProducts();
    }

    /**
     * Traiter un seul produit
     */
    private function handleSingleProduct($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            $this->error("❌ Produit #{$productId} introuvable");
            return 1;
        }

        $this->info("🔄 Génération d'image pour: {$product->name}");

        if ($this->option('force')) {
            $imagePath = $this->imageService->regenerateProductImage($product);
        } else {
            $imagePath = $this->imageService->generateProductImage($product);
        }

        if ($imagePath) {
            $this->info("✅ Image générée: {$imagePath}");
        } else {
            $this->error("❌ Échec de la génération");
        }

        return 0;
    }

    /**
     * Traiter plusieurs produits
     */
    private function handleMultipleProducts()
    {
        $limit = (int) $this->option('limit');
        $force = $this->option('force');

        // Construire la requête
        $query = Product::query();

        if (!$force) {
            // Seulement les produits sans images
            $query->where(function($q) {
                $q->whereNull('images')
                  ->orWhere('images', '[]')
                  ->orWhere('images', '');
            });
        }

        $products = $query->limit($limit)->get();

        if ($products->isEmpty()) {
            $this->info('✨ Tous les produits ont déjà des images !');
            return 0;
        }

        $this->info("📊 {$products->count()} produit(s) à traiter");
        $this->newLine();

        // Barre de progression
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0
        ];

        foreach ($products as $product) {
            $bar->setMessage("Traitement: {$product->name}");

            try {
                if ($force) {
                    $imagePath = $this->imageService->regenerateProductImage($product);
                } else {
                    $imagePath = $this->imageService->generateProductImage($product);
                }

                if ($imagePath) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                }

            } catch (\Exception $e) {
                $results['failed']++;
                $this->newLine();
                $this->error("❌ Erreur pour {$product->name}: " . $e->getMessage());
            }

            $bar->advance();

            // Pause pour éviter de surcharger les APIs
            sleep(1);
        }

        $bar->finish();
        $this->newLine(2);

        // Afficher les résultats
        $this->displayResults($results);

        return 0;
    }

    /**
     * Afficher les résultats
     */
    private function displayResults(array $results)
    {
        $this->info('📊 Résultats de la génération:');
        $this->table(
            ['Statut', 'Nombre'],
            [
                ['✅ Succès', $results['success']],
                ['❌ Échecs', $results['failed']],
                ['⏭️ Ignorés', $results['skipped']]
            ]
        );

        $total = array_sum($results);
        $successRate = $total > 0 ? round(($results['success'] / $total) * 100, 1) : 0;

        $this->newLine();
        $this->info("🎯 Taux de réussite: {$successRate}%");

        if ($results['success'] > 0) {
            $this->info('✨ Les images ont été générées et sauvegardées dans storage/app/public/products/');
        }
    }
}
