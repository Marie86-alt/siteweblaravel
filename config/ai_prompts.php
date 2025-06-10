<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Prompts pour génération d'images de produits
    |--------------------------------------------------------------------------
    |
    | Ces prompts sont utilisés par Claude pour créer des descriptions
    | détaillées qui serviront ensuite à générer les images via DALL-E
    |
    */

    'product_image_prompt' => [
        'base_prompt' => "Je vends des fruits et légumes bio en ligne. J'ai besoin d'une description détaillée pour générer une image marketing du produit suivant :

Nom: {product_name}
Catégorie: {category_name}
Description: {product_description}
Prix: {product_price}€ / {product_unit}

Peux-tu me donner une description détaillée et professionnelle pour générer une image marketing de ce produit ?",

        'style_requirements' => [
            'Fond blanc ou neutre très propre',
            'Éclairage naturel et doux',
            'Style épuré et professionnel',
            'Parfait pour un site e-commerce',
            'Mettant en valeur la fraîcheur du produit',
            'Composition centrée et équilibrée',
            'Haute qualité photographique',
            'Sans texte ni étiquette visible'
        ],

        'quality_keywords' => [
            'ultra-realistic',
            'high-resolution',
            'professional photography',
            'commercial grade',
            'studio lighting',
            'macro details',
            'vibrant colors',
            'crisp and sharp'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Prompts spécialisés par catégorie
    |--------------------------------------------------------------------------
    */

    'category_specific_prompts' => [
        'fruits' => [
            'additional_description' => 'Mets l\'accent sur la jutosité, la fraîcheur et les couleurs vives du fruit. Montre sa texture naturelle et son aspect appétissant.',
            'composition' => 'Fruit entier avec éventuellement une tranche pour montrer l\'intérieur juteux',
            'lighting' => 'Éclairage qui fait ressortir les reflets naturels de la peau du fruit'
        ],

        'légumes' => [
            'additional_description' => 'Souligne la fraîcheur, la fermeté et les couleurs naturelles du légume. Montre sa forme caractéristique et sa texture.',
            'composition' => 'Légume entier ou coupé pour révéler sa structure interne si pertinent',
            'lighting' => 'Éclairage qui accentue les détails de surface et la texture'
        ],

        'herbes aromatiques' => [
            'additional_description' => 'Capture la fraîcheur des feuilles, leur couleur verte vive et leur aspect délicat. Suggère l\'arôme par l\'image.',
            'composition' => 'Bouquet ou plante en pot avec feuilles bien visibles',
            'lighting' => 'Éclairage doux qui fait ressortir les nervures des feuilles'
        ],

        'fruits rouges' => [
            'additional_description' => 'Met en valeur les couleurs rouge/rosé intenses, la délicatesse et l\'aspect juteux des petits fruits.',
            'composition' => 'Groupe de fruits ou barquette élégante montrant la qualité',
            'lighting' => 'Éclairage qui fait briller les couleurs et la surface des fruits'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration technique
    |--------------------------------------------------------------------------
    */

    'technical_specs' => [
        'image_size' => '1024x1024',
        'aspect_ratio' => 'square',
        'quality' => 'hd',
        'style' => 'natural',
        'color_space' => 'sRGB',
        'format' => 'JPEG'
    ],

    /*
    |--------------------------------------------------------------------------
    | Messages d'erreur personnalisés
    |--------------------------------------------------------------------------
    */

    'error_messages' => [
        'api_limit_reached' => 'Limite d\'API atteinte. Veuillez réessayer plus tard.',
        'invalid_product' => 'Produit invalide pour la génération d\'image.',
        'network_error' => 'Erreur de connexion. Vérifiez votre connexion internet.',
        'ai_service_unavailable' => 'Service d\'IA temporairement indisponible.',
        'image_generation_failed' => 'Échec de la génération d\'image. Veuillez réessayer.',
        'storage_error' => 'Erreur lors de la sauvegarde de l\'image.',
        'insufficient_description' => 'Description du produit insuffisante pour générer une image de qualité.'
    ],

    /*
    |--------------------------------------------------------------------------
    | Personnalisation par boutique
    |--------------------------------------------------------------------------
    */

    'shop_branding' => [
        'style_guide' => 'Style naturel et bio, couleurs fraîches et authentiques',
        'brand_keywords' => ['bio', 'naturel', 'frais', 'local', 'authentique', 'qualité'],
        'avoid_keywords' => ['artificiel', 'industriel', 'plastique', 'emballage'],
        'color_palette' => ['vert naturel', 'blanc pur', 'tons terreux', 'couleurs vives naturelles']
    ]

];
