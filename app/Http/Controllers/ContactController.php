<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class ContactController extends Controller
{
    /**
     * Afficher la page de contact
     */
    public function index()
    {
        try {
            Log::info('Affichage de la page de contact');

            // Informations de contact (peuvent venir de la config ou base de données)
            $contactInfo = [
                'phone_commercial' => config('contact.phone_commercial', '01 23 45 67 89'),
                'phone_urgency' => config('contact.phone_urgency', '01 23 45 67 90'),
                'email_commercial' => config('contact.email_commercial', 'commercial@fruits-legumes.fr'),
                'email_support' => config('contact.email_support', 'support@fruits-legumes.fr'),
                'address' => config('contact.address', [
                    'street' => '123 Avenue du Marché',
                    'zone' => 'Zone Industrielle Sud',
                    'postal_code' => '75001',
                    'city' => 'Paris',
                    'country' => 'France'
                ]),
                'delivery_zone' => config('contact.delivery_zone', 'Île-de-France et régions limitrophes'),
                'business_hours' => config('contact.business_hours', [
                    'monday_friday' => '6h00 - 18h00',
                    'saturday' => '7h00 - 15h00',
                    'sunday' => 'Fermé',
                    'emergency' => '24h/7j'
                ])
            ];

            return view('contact.index', compact('contactInfo'));

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage de la page de contact', [
                'message' => $e->getMessage()
            ]);

            return view('contact.index', [
                'contactInfo' => $this->getDefaultContactInfo()
            ]);
        }
    }

    /**
     * Traiter l'envoi du formulaire de contact
     */
    public function store(Request $request)
    {
        try {
            Log::info('Début ContactController::store', $request->except(['_token']));

            // Validation des données
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'request_type' => 'required|in:devis,partenariat,livraison,produit,autre',
                'volume' => 'nullable|in:small,medium,large,enterprise',
                'message' => 'required|string|min:10|max:2000',
            ], [
                'first_name.required' => 'Le prénom est obligatoire.',
                'last_name.required' => 'Le nom est obligatoire.',
                'company.required' => 'Le nom de l\'entreprise est obligatoire.',
                'email.required' => 'L\'email est obligatoire.',
                'email.email' => 'L\'email doit être valide.',
                'phone.required' => 'Le téléphone est obligatoire.',
                'request_type.required' => 'Le type de demande est obligatoire.',
                'request_type.in' => 'Le type de demande sélectionné n\'est pas valide.',
                'message.required' => 'Le message est obligatoire.',
                'message.min' => 'Le message doit contenir au moins 10 caractères.',
                'message.max' => 'Le message ne peut pas dépasser 2000 caractères.',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation échouée', $validator->errors()->toArray());
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validatedData = $validator->validated();
            Log::info('Validation réussie', array_keys($validatedData));

            // Préparation des données pour l'email
            $contactData = [
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'company' => $validatedData['company'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'request_type' => $this->getRequestTypeLabel($validatedData['request_type']),
                'volume' => $this->getVolumeLabel($validatedData['volume'] ?? null),
                'message' => $validatedData['message'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'submitted_at' => now()->format('d/m/Y H:i:s'),
            ];

            // Envoi de l'email à l'équipe commerciale
            $this->sendEmailToTeam($contactData);

            // Envoi de l'email de confirmation au client
            $this->sendConfirmationEmail($contactData);

            // Optionnel : Sauvegarder en base de données
            $this->saveContactRequest($contactData);

            Log::info('Demande de contact traitée avec succès', [
                'email' => $contactData['email'],
                'company' => $contactData['company']
            ]);

            return redirect()->back()
                ->with('success', 'Votre demande a été envoyée avec succès ! Nous vous recontacterons dans les plus brefs délais.');

        } catch (\Exception $e) {
            Log::error('Erreur dans ContactController::store', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'envoi de votre demande. Veuillez réessayer ou nous contacter par téléphone.');
        }
    }

    /**
     * API pour vérifier la disponibilité (AJAX)
     */
    public function checkAvailability(Request $request)
    {
        try {
            $currentHour = now()->hour;
            $currentDay = now()->dayOfWeek; // 0 = Dimanche, 1 = Lundi, etc.

            $isOpen = false;
            $nextOpenTime = null;

            // Logique des horaires d'ouverture
            if ($currentDay >= 1 && $currentDay <= 5) { // Lundi à Vendredi
                $isOpen = ($currentHour >= 6 && $currentHour < 18);
                if (!$isOpen && $currentHour < 6) {
                    $nextOpenTime = 'Ouverture à 6h00';
                } elseif (!$isOpen && $currentHour >= 18) {
                    $nextOpenTime = 'Ouverture demain à 6h00';
                }
            } elseif ($currentDay == 6) { // Samedi
                $isOpen = ($currentHour >= 7 && $currentHour < 15);
                if (!$isOpen && $currentHour < 7) {
                    $nextOpenTime = 'Ouverture à 7h00';
                } elseif (!$isOpen && $currentHour >= 15) {
                    $nextOpenTime = 'Ouverture lundi à 6h00';
                }
            } else { // Dimanche
                $nextOpenTime = 'Ouverture lundi à 6h00';
            }

            return response()->json([
                'is_open' => $isOpen,
                'next_open_time' => $nextOpenTime,
                'current_time' => now()->format('H:i'),
                'emergency_available' => true // Urgences 24h/24
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur dans checkAvailability', ['message' => $e->getMessage()]);

            return response()->json([
                'is_open' => false,
                'message' => 'Impossible de vérifier la disponibilité'
            ], 500);
        }
    }

    /**
     * Envoyer l'email à l'équipe commerciale
     */
    private function sendEmailToTeam(array $contactData)
    {
        try {
            $to = config('contact.email_commercial', 'commercial@fruits-legumes.fr');

            Mail::send('emails.contact.team-notification', $contactData, function ($message) use ($to, $contactData) {
                $message->to($to)
                    ->subject('[DEMANDE DEVIS] ' . $contactData['company'] . ' - ' . $contactData['request_type'])
                    ->replyTo($contactData['email'], $contactData['first_name'] . ' ' . $contactData['last_name']);
            });

            Log::info('Email envoyé à l\'équipe commerciale', ['to' => $to]);

        } catch (\Exception $e) {
            Log::error('Erreur envoi email équipe', ['message' => $e->getMessage()]);
            // Ne pas faire échouer le processus si l'email ne part pas
        }
    }

    /**
     * Envoyer l'email de confirmation au client
     */
    private function sendConfirmationEmail(array $contactData)
    {
        try {
            Mail::send('emails.contact.client-confirmation', $contactData, function ($message) use ($contactData) {
                $message->to($contactData['email'], $contactData['first_name'] . ' ' . $contactData['last_name'])
                    ->subject('Confirmation de votre demande - Fruits & Légumes')
                    ->from(config('contact.email_commercial', 'commercial@fruits-legumes.fr'), 'Équipe Commerciale');
            });

            Log::info('Email de confirmation envoyé', ['to' => $contactData['email']]);

        } catch (\Exception $e) {
            Log::error('Erreur envoi email confirmation', ['message' => $e->getMessage()]);
            // Ne pas faire échouer le processus si l'email ne part pas
        }
    }

    /**
     * Sauvegarder la demande en base de données (optionnel)
     */
    private function saveContactRequest(array $contactData)
    {
        try {
            // Si vous avez une table contact_requests
            /*
            \App\Models\ContactRequest::create([
                'first_name' => $contactData['first_name'],
                'last_name' => $contactData['last_name'],
                'company' => $contactData['company'],
                'email' => $contactData['email'],
                'phone' => $contactData['phone'],
                'request_type' => $contactData['request_type'],
                'volume' => $contactData['volume'],
                'message' => $contactData['message'],
                'ip_address' => $contactData['ip_address'],
                'user_agent' => $contactData['user_agent'],
                'status' => 'pending',
            ]);
            */

            Log::info('Demande sauvegardée en base de données');

        } catch (\Exception $e) {
            Log::error('Erreur sauvegarde base de données', ['message' => $e->getMessage()]);
            // Ne pas faire échouer le processus
        }
    }

    /**
     * Obtenir le libellé du type de demande
     */
    private function getRequestTypeLabel($type)
    {
        $types = [
            'devis' => 'Demande de devis',
            'partenariat' => 'Partenariat commercial',
            'livraison' => 'Information livraison',
            'produit' => 'Question produit',
            'autre' => 'Autre demande'
        ];

        return $types[$type] ?? $type;
    }

    /**
     * Obtenir le libellé du volume
     */
    private function getVolumeLabel($volume)
    {
        if (!$volume) return 'Non spécifié';

        $volumes = [
            'small' => 'Moins de 500€ HT/mois',
            'medium' => '500€ - 2000€ HT/mois',
            'large' => '2000€ - 5000€ HT/mois',
            'enterprise' => 'Plus de 5000€ HT/mois'
        ];

        return $volumes[$volume] ?? $volume;
    }

    /**
     * Informations de contact par défaut
     */
    private function getDefaultContactInfo()
    {
        return [
            'phone_commercial' => '01 23 45 67 89',
            'phone_urgency' => '01 23 45 67 90',
            'email_commercial' => 'commercial@fruits-legumes.fr',
            'email_support' => 'support@fruits-legumes.fr',
            'address' => [
                'street' => '123 Avenue du Marché',
                'zone' => 'Zone Industrielle Sud',
                'postal_code' => '75001',
                'city' => 'Paris',
                'country' => 'France'
            ],
            'delivery_zone' => 'Île-de-France et régions limitrophes',
            'business_hours' => [
                'monday_friday' => '6h00 - 18h00',
                'saturday' => '7h00 - 15h00',
                'sunday' => 'Fermé',
                'emergency' => '24h/7j'
            ]
        ];
    }
}
