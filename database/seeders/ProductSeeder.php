<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product; // Assuming you have a Product model
use App\Models\Category; // Assuming you have a Category model

class ProductSeeder extends Seeder
{

    public function run(): void
    {
        // Récupérer les catégories
        $fruits = Category::where('name', 'Fruits')->first();
        $legumes = Category::where('name', 'Légumes')->first();
        $fruitsRouges = Category::where('name', 'Fruits rouges')->first();
        $agrumes = Category::where('name', 'Agrumes')->first();
        $legumesRacines = Category::where('name', 'Légumes racines')->first();
        $legumesVerts = Category::where('name', 'Légumes verts')->first();
        $herbes = Category::where('name', 'Herbes aromatiques')->first();

        $products = [
            // FRUITS
            [
                'name' => 'Pommes Golden',
                'description' => 'Pommes Golden délicieuses, croquantes et sucrées. Parfaites pour croquer ou cuisiner. Cultivées dans nos vergers partenaires avec le plus grand soin.',
                'short_description' => 'Pommes Golden croquantes et sucrées',
                'price' => 2.50,
                'compare_price' => 3.20,
                'unit' => 'kg',
                'weight' => 0.150,
                'stock_quantity' => 50,
                'min_stock' => 10,
                'category_id' => $fruits->id,
                'origin' => 'France - Vallée de la Loire',
                'is_active' => true,
                'is_featured' => true,
                'is_bio' => false
            ],
            [
                'name' => 'Bananes Cavendish',
                'description' => 'Bananes mûres et savoureuses, riches en potassium et vitamines. Issues du commerce équitable pour soutenir les producteurs.',
                'short_description' => 'Bananes mûres et savoureuses',
                'price' => 2.80,
                'unit' => 'kg',
                'stock_quantity' => 30,
                'min_stock' => 5,
                'category_id' => $fruits->id,
                'origin' => 'République Dominicaine',
                'is_active' => true,
                'is_featured' => false,
                'is_bio' => true
            ],
            [
                'name' => 'Poires Conference',
                'description' => 'Poires Conference juteuses et parfumées, idéales en dessert ou en cuisine. Récoltées à parfaite maturité.',
                'short_description' => 'Poires Conference juteuses',
                'price' => 3.20,
                'unit' => 'kg',
                'stock_quantity' => 25,
                'min_stock' => 8,
                'category_id' => $fruits->id,
                'origin' => 'France - Provence',
                'is_active' => true,
                'is_featured' => true,
                'is_bio' => true
            ],
            [
                'name' => 'Kiwis de France',
                'description' => 'Kiwis français gorgés de vitamine C. Chair verte acidulée et rafraîchissante. Parfaits pour commencer la journée.',
                'short_description' => 'Kiwis français riches en vitamine C',
                'price' => 4.20,
                'unit' => 'kg',
                'stock_quantity' => 20,
                'min_stock' => 5,
                'category_id' => $fruits->id,
                'origin' => 'France - Sud-Ouest',
                'is_active' => true,
                'is_featured' => false,
                'is_bio' => false
            ],

            // LÉGUMES
            [
                'name' => 'Tomates cerises',
                'description' => 'Tomates cerises sucrées et croquantes, parfaites pour les salades et apéritifs. Cultivées sous serre avec amour.',
                'short_description' => 'Tomates cerises sucrées',
                'price' => 4.50,
                'unit' => 'barquette',
                'weight' => 0.250,
                'stock_quantity' => 40,
                'min_stock' => 10,
                'category_id' => $legumes->id,
                'origin' => 'France - Provence',
                'is_active' => true,
                'is_featured' => true,
                'is_bio' => false
            ],
            [
                'name' => 'Courgettes',
                'description' => 'Courgettes fraîches et tendres, excellentes grillées, en ratatouille ou en soupe. Récoltées jeunes pour plus de tendreté.',
                'short_description' => 'Courgettes fraîches et tendres',
                'price' => 2.20,
                'unit' => 'kg',
                'stock_quantity' => 35,
                'min_stock' => 8,
                'category_id' => $legumesVerts->id,
                'origin' => 'France - Languedoc',
                'is_active' => true,
                'is_featured' => false,
                'is_bio' => true
            ],
            [
                'name' => 'Salade batavia',
                'description' => 'Salade batavia fraîche et croquante, cultivée localement. Feuilles tendres et goût délicat.',
                'short_description' => 'Salade batavia fraîche',
                'price' => 1.80,
                'unit' => 'pièce',
                'weight' => 0.300,
                'stock_quantity' => 20,
                'min_stock' => 5,
                'category_id' => $legumesVerts->id,
                'origin' => 'France - Île-de-France',
                'is_active' => true,
                'is_featured' => false,
                'is_bio' => false
            ],
            [
                'name' => 'Épinards frais',
                'description' => 'Épinards frais en feuilles, riches en fer et vitamines. Parfaits pour les salades ou cuisinés.',
                'short_description' => 'Épinards frais riches en fer',
                'price' => 3.20,
                'unit' => 'botte',
                'weight' => 0.200,
                'stock_quantity' => 15,
                'min_stock' => 3,
                'category_id' => $legumesVerts->id,
                'origin' => 'France - Nord',
                'is_active' => true,
                'is_featured' => true,
                'is_bio' => true
            ],

            // FRUITS ROUGES
            [
                'name' => 'Fraises Gariguette',
                'description' => 'Fraises Gariguette parfumées et sucrées, la star du printemps. Cultivées avec passion par nos producteurs locaux.',
                'short_description' => 'Fraises Gariguette parfumées',
                'price' => 6.50,
                'compare_price' => 7.80,
                'unit' => 'barquette',
                'weight' => 0.500,
                'stock_quantity' => 15,
                'min_stock' => 3,
                'category_id' => $fruitsRouges->id,
                'origin' => 'France - Périgord',
                'is_active' => true,
                'is_featured' => true,
                'is_bio' => false,
                'expiry_date' => now()->addDays(3)
            ],
            [
                'name' => 'Framboises',
                'description' => 'Framboises délicates et parfumées, riches en antioxydants. Fraîcheur et saveur incomparables.',
                'short_description' => 'Framboises délicates',
                'price' => 8.90,
                'unit' => 'barquette',
                'weight' => 0.250,
                'stock_quantity' => 12,
                'min_stock' => 2,
                'category_id' => $fruitsRouges->id,
                'origin' => 'France - Rhône-Alpes',
                'is_active' => true,
                'is_featured' => false,
                'is_bio' => true,
                'expiry_date' => now()->addDays(2)
            ],
            [
                'name' => 'Myrtilles',
                'description' => 'Myrtilles sauvages gorgées d\'antioxydants. Parfaites nature ou en pâtisserie.',
                'short_description' => 'Myrtilles sauvages antioxydantes',
                'price' => 7.20,
                'unit' => 'barquette',
                'weight' => 0.200,
                'stock_quantity' => 10,
                'min_stock' => 2,
                'category_id' => $fruitsRouges->id,
                'origin' => 'France - Vosges',
                'is_active' => true,
                'is_featured' => true,
                'is_bio' => true
            ],

            // AGRUMES
            [
                'name' => 'Oranges navel',
                'description' => 'Oranges navel juteuses et sans pépins, parfaites pour les jus et desserts. Gorgées de vitamine C.',
                'short_description' => 'Oranges navel juteuses',
                'price' => 2.90,
                'unit' => 'kg',
                'stock_quantity' => 45,
                'min_stock' => 10,
                'category_id' => $agrumes->id,
                'origin' => 'Espagne - Valence',
                'is_active' => true,
                'is_featured' => false,
                'is_bio' => false
            ],
            [
                'name' => 'Citrons jaunes',
                'description' => 'Citrons jaunes parfumés, indispensables en cuisine et pâtisserie. Écorce non traitée.',
                'short_description' => 'Citrons jaunes parfumés',
                'price' => 3.50,
                'unit' => 'kg',
                'stock_quantity' => 30,
                'min_stock' => 8,
                'category_id' => $agrumes->id,
                'origin' => 'Italie - Sicile',
                'is_active' => true,
                'is_featured' => false,
                'is_bio' => true
            ],
            [
                'name' => 'Pamplemousses roses',
                'description' => 'Pamplemousses roses juteux et rafraîchissants. Chair tendre et légèrement sucrée.',
                'short_description' => 'Pamplemousses roses juteux',
                'price' => 3.80,
                'unit' => 'kg',
                'stock_quantity' => 25,
                'min_stock' => 5,
                'category_id' => $agrumes->id,
                'origin' => 'France - Corse',
                'is_active' => true,
                'is_featured' => true,
                'is_bio' => false
            ],

            // LÉGUMES RACINES
            [
                'name' => 'Carottes nouvelles',
                'description' => 'Carottes nouvelles croquantes et sucrées, parfaites crues ou cuites. Riches en bêta-carotène.',
                'short_description' => 'Carottes nouvelles croquantes',
                'price' => 1.80,
                'unit' => 'kg',
                'stock_quantity' => 40,
                'min_stock' => 10,
                'category_id' => $legumesRacines->id,
                'origin' => 'France - Normandie',
                'is_active' => true,
                'is_featured' => true,
                'is_bio' => false
            ],
            [
                'name' => 'Radis roses',
                'description' => 'Radis roses croquants et piquants, parfaits pour l\'apéritif. Fraîchement récoltés.',
                'short_description' => 'Radis roses croquants',
                'price' => 1.20,
                'unit' => 'botte',
                'weight' => 0.200,
                'stock_quantity' => 25,
                'min_stock' => 5,
                'category_id' => $legumesRacines->id,
                'origin' => 'France - Val de Loire',
                'is_active' => true,
                'is_featured' => false,
                'is_bio' => true
            ],
            [
                'name' => 'Betteraves rouges',
                'description' => 'Betteraves rouges sucrées et tendres. Excellentes en salade ou cuites. Riches en antioxydants.',
                'short_description' => 'Betteraves rouges sucrées',
                'price' => 2.40,
                'unit' => 'kg',
                'stock_quantity' => 20,
                'min_stock' => 5,
                'category_id' => $legumesRacines->id,
                'origin' => 'France - Picardie',
                'is_active' => true,
                'is_featured' => false,
                'is_bio' => false
            ],

            // HERBES AROMATIQUES
            [
                'name' => 'Basilic frais',
                'description' => 'Basilic frais aux feuilles parfumées. Indispensable pour la cuisine méditerranéenne.',
                'short_description' => 'Basilic frais parfumé',
                'price' => 1.50,
                'unit' => 'pot',
                'weight' => 0.030,
                'stock_quantity' => 30,
                'min_stock' => 5,
                'category_id' => $herbes->id,
                'origin' => 'France - Provence',
                'is_active' => true,
                'is_featured' => false,
                'is_bio' => true
            ],
            [
                'name' => 'Persil plat',
                'description' => 'Persil plat frais, plus parfumé que le persil frisé. Essentiel en cuisine.',
                'short_description' => 'Persil plat frais',
                'price' => 1.20,
                'unit' => 'botte',
                'weight' => 0.050,
                'stock_quantity' => 25,
                'min_stock' => 5,
                'category_id' => $herbes->id,
                'origin' => 'France - Île-de-France',
                'is_active' => true,
                'is_featured' => false,
                'is_bio' => false
            ],
            [
                'name' => 'Thym frais',
                'description' => 'Thym frais aux arômes intenses. Parfait pour parfumer vos plats mijotés.',
                'short_description' => 'Thym frais aromatique',
                'price' => 1.80,
                'unit' => 'botte',
                'weight' => 0.020,
                'stock_quantity' => 20,
                'min_stock' => 3,
                'category_id' => $herbes->id,
                'origin' => 'France - Provence',
                'is_active' => true,
                'is_featured' => false,
                'is_bio' => true
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
}
}
