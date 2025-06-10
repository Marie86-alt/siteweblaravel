<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Informations de Contact Entreprise
    |--------------------------------------------------------------------------
    |
    | Ces informations sont utilisées dans toute l'application :
    | - Pages de contact
    | - Footer du site
    | - Emails automatiques
    | - Factures et documents
    |
    */

    // Téléphones
    'phone_commercial' => env('CONTACT_PHONE_COMMERCIAL', '01 23 45 67 89'),
    'phone_urgency' => env('CONTACT_PHONE_URGENCY', '01 23 45 67 90'),
    'phone_fax' => env('CONTACT_PHONE_FAX', '01 23 45 67 91'),

    // Emails
    'email_commercial' => env('CONTACT_EMAIL_COMMERCIAL', 'commercial@fruits-legumes.fr'),
    'email_support' => env('CONTACT_EMAIL_SUPPORT', 'support@fruits-legumes.fr'),
    'email_comptabilite' => env('CONTACT_EMAIL_COMPTABILITE', 'comptabilite@fruits-legumes.fr'),
    'email_direction' => env('CONTACT_EMAIL_DIRECTION', 'direction@fruits-legumes.fr'),

    // Adresse principale
    'address' => [
        'company_name' => env('CONTACT_COMPANY_NAME', 'Fruits & Légumes Distribution'),
        'street' => env('CONTACT_ADDRESS_STREET', '123 Avenue du Marché'),
        'street_complement' => env('CONTACT_ADDRESS_COMPLEMENT', 'Zone Industrielle Sud'),
        'postal_code' => env('CONTACT_POSTAL_CODE', '75001'),
        'city' => env('CONTACT_CITY', 'Paris'),
        'country' => env('CONTACT_COUNTRY', 'France'),
    ],

    // Informations légales
    'legal' => [
        'siret' => env('CONTACT_SIRET', '123 456 789 00012'),
        'siren' => env('CONTACT_SIREN', '123 456 789'),
        'code_ape' => env('CONTACT_CODE_APE', '4631Z'),
        'tva_number' => env('CONTACT_TVA_NUMBER', 'FR12345678901'),
        'capital_social' => env('CONTACT_CAPITAL_SOCIAL', '50000'),
    ],

    // Zone de livraison
    'delivery' => [
        'zone_principale' => env('CONTACT_DELIVERY_ZONE', 'Île-de-France et régions limitrophes'),
        'zone_etendue' => env('CONTACT_DELIVERY_EXTENDED', 'France métropolitaine'),
        'frais_port_gratuit' => env('CONTACT_FREE_SHIPPING_FROM', '200'), // en euros HT
        'delai_standard' => env('CONTACT_DELIVERY_DELAY', '24-48h'),
        'delai_express' => env('CONTACT_EXPRESS_DELAY', '4h'),
    ],

    // Horaires d'ouverture
    'business_hours' => [
        'monday' => ['open' => '06:00', 'close' => '18:00'],
        'tuesday' => ['open' => '06:00', 'close' => '18:00'],
        'wednesday' => ['open' => '06:00', 'close' => '18:00'],
        'thursday' => ['open' => '06:00', 'close' => '18:00'],
        'friday' => ['open' => '06:00', 'close' => '18:00'],
        'saturday' => ['open' => '07:00', 'close' => '15:00'],
        'sunday' => ['open' => null, 'close' => null], // Fermé

        // Labels pour affichage
        'labels' => [
            'monday_friday' => 'Lundi - Vendredi : 6h00 - 18h00',
            'saturday' => 'Samedi : 7h00 - 15h00',
            'sunday' => 'Dimanche : Fermé',
            'emergency' => 'Urgences : 24h/7j'
        ]
    ],

    // Réseaux sociaux
    'social_media' => [
        'facebook' => env('CONTACT_FACEBOOK', ''),
        'instagram' => env('CONTACT_INSTAGRAM', ''),
        'linkedin' => env('CONTACT_LINKEDIN', ''),
        'twitter' => env('CONTACT_TWITTER', ''),
        'youtube' => env('CONTACT_YOUTUBE', ''),
    ],

    // Configuration des emails
    'email_settings' => [
        'from_name' => env('CONTACT_EMAIL_FROM_NAME', 'Équipe Commerciale'),
        'reply_to' => env('CONTACT_EMAIL_REPLY_TO', 'commercial@fruits-legumes.fr'),
        'auto_response' => env('CONTACT_AUTO_RESPONSE', true),
        'notification_emails' => [
            env('CONTACT_EMAIL_COMMERCIAL', 'commercial@fruits-legumes.fr'),
            env('CONTACT_EMAIL_DIRECTION', 'direction@fruits-legumes.fr'),
        ],
    ],

    // Messages automatiques
    'messages' => [
        'contact_success' => 'Votre demande a été envoyée avec succès ! Nous vous recontacterons dans les plus brefs délais.',
        'contact_error' => 'Une erreur est survenue lors de l\'envoi de votre demande. Veuillez réessayer ou nous contacter par téléphone.',
        'outside_hours' => 'Nous sommes actuellement fermés. Nous vous répondrons dès la réouverture.',
        'emergency_notice' => 'Pour les urgences, contactez-nous au {phone_urgency}',
    ],

    // Coordonnées GPS (pour Google Maps)
    'coordinates' => [
        'latitude' => env('CONTACT_LATITUDE', '48.8566'),
        'longitude' => env('CONTACT_LONGITUDE', '2.3522'),
        'google_maps_url' => env('CONTACT_GOOGLE_MAPS_URL', 'https://maps.google.com/'),
    ],

    // Informations bancaires (si nécessaire pour factures)
    'banking' => [
        'bank_name' => env('CONTACT_BANK_NAME', 'Banque de France'),
        'iban' => env('CONTACT_IBAN', ''),
        'bic' => env('CONTACT_BIC', ''),
        'rib' => env('CONTACT_RIB', ''),
    ],

];
