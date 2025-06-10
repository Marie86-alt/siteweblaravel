<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
   public function run(): void
    {
        $categories = [
            [
                'name' => 'Fruits',
                'description' => 'Fruits frais de saison, cueillis à maturité pour vous offrir le meilleur des saveurs.',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Légumes',
                'description' => 'Légumes frais du potager, cultivés avec soin par nos producteurs locaux.',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Fruits rouges',
                'description' => 'Fraises, framboises, cerises... La délicatesse des fruits rouges de saison.',
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Agrumes',
                'description' => 'Oranges, citrons, pamplemousses... Vitamines et fraîcheur garanties.',
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Légumes racines',
                'description' => 'Carottes, radis, betteraves... Les trésors cachés de la terre.',
                'sort_order' => 5,
                'is_active' => true
            ],
            [
                'name' => 'Légumes verts',
                'description' => 'Épinards, salade, courgettes... La fraîcheur et la santé dans votre assiette.',
                'sort_order' => 6,
                'is_active' => true
            ],
            [
                'name' => 'Produits bio',
                'description' => 'Notre sélection certifiée agriculture biologique, sans pesticides ni engrais chimiques.',
                'sort_order' => 7,
                'is_active' => true
            ],
            [
                'name' => 'Herbes aromatiques',
                'description' => 'Basilic, thym, persil... Pour parfumer vos plats avec des saveurs authentiques.',
                'sort_order' => 8,
                'is_active' => true
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }
    }
}
