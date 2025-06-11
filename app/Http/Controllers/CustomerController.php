<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\OrderNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class CustomerController extends Controller
{
    /**
     * Dashboard principal amélioré
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Statistiques de base
        $totalOrders = $user->orders()->count();
        $totalSpent = $user->orders()->whereNotIn('status', ['cancelled'])->sum('total_amount');
        $pendingOrders = $user->orders()->where('status', 'pending')->count();
        $lastOrder = $user->orders()->latest()->first();

        // Calcul des tendances mensuelles
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();

        $currentMonthOrders = $user->orders()->where('created_at', '>=', $currentMonth)->count();
        $previousMonthOrders = $user->orders()
            ->whereBetween('created_at', [$previousMonth, $currentMonth])
            ->count();

        $currentMonthSpent = $user->orders()
            ->where('created_at', '>=', $currentMonth)
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_amount');
        $previousMonthSpent = $user->orders()
            ->whereBetween('created_at', [$previousMonth, $currentMonth])
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_amount');

        // Calcul des pourcentages de tendance
        $ordersTrend = $previousMonthOrders > 0
            ? (($currentMonthOrders - $previousMonthOrders) / $previousMonthOrders) * 100
            : 0;
        $spendingTrend = $previousMonthSpent > 0
            ? (($currentMonthSpent - $previousMonthSpent) / $previousMonthSpent) * 100
            : 0;

        // Statistiques complètes
        $stats = [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'total_spent' => $totalSpent,
            'last_order' => $lastOrder,
            'orders_trend' => round($ordersTrend, 1),
            'spending_trend' => round($spendingTrend, 1),
        ];

        // Données pour le graphique
        $chartData = $this->getChartData($user, 6);

        // Commandes récentes
        $recent_orders = $user->orders()
                            ->with('orderItems.product')
                            ->latest()
                            ->take(5)
                            ->get();

        // Produits recommandés
        $recommended_products = $this->getRecommendedProducts($user);

        // Notifications
        $notifications = $user->notifications()
            ->latest()
            ->take(5)
            ->get();

        $unreadNotifications = $user->unreadNotifications()->count();

        return view('customer.dashboard', compact(
            'user',
            'stats',
            'recent_orders',
            'recommended_products',
            'chartData',
            'notifications',
            'unreadNotifications'
        ));
    }

    /**
     * Obtenir les données pour le graphique - VERSION SQLITE COMPATIBLE
     */
    private function getChartData($user, $months = 6)
    {
        $startDate = Carbon::now()->subMonths($months)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Récupérer toutes les commandes de la période
        $orders = $user->orders()
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->whereNotIn('status', ['cancelled'])
            ->select('created_at', 'total_amount')
            ->get();

        // Préparer les données pour Chart.js
        $labels = [];
        $ordersCount = [];
        $amounts = [];

        // Créer les données pour chaque mois
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $monthLabel = $month->format('M Y');

            $labels[] = $monthLabel;

            // Filtrer les commandes pour ce mois
            $monthOrders = $orders->filter(function ($order) use ($monthKey) {
                return $order->created_at->format('Y-m') === $monthKey;
            });

            $ordersCount[] = $monthOrders->count();
            $amounts[] = $monthOrders->sum('total_amount');
        }

        return [
            'labels' => $labels,
            'orders' => $ordersCount,
            'amounts' => $amounts
        ];
    }

    /**
     * Obtenir des produits recommandés
     */
    private function getRecommendedProducts($user)
    {
        // Si l'utilisateur a des commandes, recommander des produits similaires
        if ($user->orders()->count() > 0) {
            // Obtenir les catégories des produits achetés
            $purchasedCategoryIds = $user->orders()
                ->with('orderItems.product')
                ->get()
                ->pluck('orderItems')
                ->flatten()
                ->pluck('product.category_id')
                ->filter()
                ->unique()
                ->toArray();

            if (!empty($purchasedCategoryIds)) {
                return Product::whereIn('category_id', $purchasedCategoryIds)
                    ->where('is_active', true)
                    ->inRandomOrder()
                    ->take(3)
                    ->get();
            }
        }

        // Sinon, produits populaires ou aléatoires
        return Product::where('is_active', true)
            ->inRandomOrder()
            ->take(3)
            ->get();
    }

    /**
     * Obtenir les données du graphique via AJAX - VERSION CORRIGÉE
     */
    public function getChartDataAjax(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', '6months');

        $months = match($period) {
            'year' => 12,
            'all' => 24, // Maximum 24 mois
            default => 6
        };

        $chartData = $this->getChartData($user, $months);

        return response()->json($chartData);
    }

    /**
     * Page profil utilisateur
     */
    public function profile()
    {
        $user = Auth::user();

        // Enrichir l'utilisateur avec les statistiques
        $user->total_orders = $user->orders()->count();
        $user->total_spent = $user->orders()->whereNotIn('status', ['cancelled'])->sum('total_amount');

        return view('customer.profile.index', compact('user'));
    }

    /**
     * Mettre à jour le profil utilisateur
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'company' => 'nullable|string|max:255',
        ], [
            'first_name.required' => 'Le prénom est obligatoire',
            'last_name.required' => 'Le nom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être valide',
            'email.unique' => 'Cet email est déjà utilisé',
            'phone.max' => 'Le téléphone ne peut pas dépasser 20 caractères',
            'birth_date.date' => 'La date de naissance doit être valide',
            'birth_date.before' => 'La date de naissance doit être antérieure à aujourd\'hui',
        ]);

        // Mettre à jour les informations
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'company' => $request->company,
        ]);

        return redirect()->route('customer.profile')
                        ->with('profile_success', 'Vos informations ont été mises à jour avec succès !');
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire',
            'new_password.required' => 'Le nouveau mot de passe est obligatoire',
            'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            'new_password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
        ]);

        // Vérifier l'ancien mot de passe
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect']);
        }

        // Mettre à jour le mot de passe
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('customer.profile')
                        ->with('password_success', 'Votre mot de passe a été modifié avec succès !');
    }

    /**
     * Liste des commandes
     */
    public function orders(Request $request)
    {
        $user = Auth::user();

        $query = $user->orders()->with('orderItems.product')->latest();

        // Filtrer par statut si spécifié
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);

        // Statistiques des commandes
        $orderStats = [
            'total' => $user->orders()->count(),
            'pending' => $user->orders()->where('status', 'pending')->count(),
            'confirmed' => $user->orders()->where('status', 'confirmed')->count(),
            'delivered' => $user->orders()->where('status', 'delivered')->count(),
            'cancelled' => $user->orders()->where('status', 'cancelled')->count(),
        ];

        return view('customer.orders.index', compact('orders', 'orderStats'));
    }

    /**
     * Détail d'une commande
     */
    public function orderShow(Order $order)
    {
        // Vérifier que la commande appartient à l'utilisateur connecté
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }

        $order->load('orderItems.product');

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Page de gestion des adresses
     */
    public function addresses()
    {
        $user = Auth::user();

        return view('customer.addresses.index', compact('user'));
    }

    /**
     * Mettre à jour les adresses
     */
    public function updateAddresses(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'billing_address' => 'nullable|string|max:500',
            'billing_city' => 'nullable|string|max:100',
            'billing_postal_code' => 'nullable|string|max:10',
            'billing_country' => 'nullable|string|max:100',
            'delivery_address' => 'nullable|string|max:500',
            'delivery_city' => 'nullable|string|max:100',
            'delivery_postal_code' => 'nullable|string|max:10',
            'delivery_country' => 'nullable|string|max:100',
            'delivery_instructions' => 'nullable|string|max:500',
        ], [
            'billing_address.max' => 'L\'adresse de facturation ne peut pas dépasser 500 caractères',
            'billing_city.max' => 'La ville de facturation ne peut pas dépasser 100 caractères',
            'billing_postal_code.max' => 'Le code postal de facturation ne peut pas dépasser 10 caractères',
            'delivery_address.max' => 'L\'adresse de livraison ne peut pas dépasser 500 caractères',
            'delivery_city.max' => 'La ville de livraison ne peut pas dépasser 100 caractères',
            'delivery_postal_code.max' => 'Le code postal de livraison ne peut pas dépasser 10 caractères',
        ]);

        $user->update([
            'billing_address' => $request->billing_address,
            'billing_city' => $request->billing_city,
            'billing_postal_code' => $request->billing_postal_code,
            'billing_country' => $request->billing_country ?: 'France',
            'delivery_address' => $request->delivery_address,
            'delivery_city' => $request->delivery_city,
            'delivery_postal_code' => $request->delivery_postal_code,
            'delivery_country' => $request->delivery_country ?: 'France',
            'delivery_instructions' => $request->delivery_instructions,
        ]);

        return redirect()->route('customer.addresses')
                        ->with('address_success', 'Vos adresses ont été mises à jour avec succès !');
    }

    /**
     * Page des favoris
     */
    public function favorites()
    {
        $user = Auth::user();

        // Pour l'instant, page simple - à développer plus tard
        $favorites = collect(); // Vide pour l'instant

        return view('customer.favorites.index', compact('favorites'));
    }

    /**
     * Page des paramètres
     */
    public function settings()
    {
        $user = Auth::user();

        return view('customer.settings.index', compact('user'));
    }

    /**
     * Mettre à jour les paramètres
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'newsletter' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
        ]);

        // Pour l'instant, juste rediriger avec un message
        // À implémenter plus tard avec les vrais champs

        return redirect()->route('customer.settings')
                        ->with('settings_success', 'Vos paramètres ont été mis à jour avec succès !');
    }

    /**
     * Marquer une notification comme lue
     */
    public function markNotificationAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllNotificationsAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Envoyer une notification de confirmation de commande
     */
    public static function sendOrderConfirmation(Order $order)
    {
        try {
            $user = $order->user;

            // Envoyer l'email de confirmation (si configuré)
            if (config('mail.default') && $user->email) {
                Mail::to($user->email)->send(new OrderConfirmation($order));
            }

            // Envoyer la notification dans l'app
            $user->notify(new OrderNotification($order, 'confirmation'));

            \Log::info("Notifications envoyées pour la commande #{$order->id}");
            return true;

        } catch (\Exception $e) {
            \Log::error('Erreur envoi email confirmation commande: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer une notification de changement de statut
     */
    public static function sendOrderStatusUpdate(Order $order, $previousStatus = null)
    {
        try {
            $user = $order->user;

            // Envoyer la notification dans l'app
            $user->notify(new OrderNotification($order, 'status_update'));

            \Log::info("Notification de statut envoyée pour la commande #{$order->id}");
            return true;

        } catch (\Exception $e) {
            \Log::error('Erreur envoi notification statut commande: ' . $e->getMessage());
            return false;
        }
    }
}
