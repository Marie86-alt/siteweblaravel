<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers Frontend
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentController;

// Controllers Admin
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\ImageGenerationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentification Laravel
Auth::routes();

// Route de redirection après login
Route::get('/home', function() {
    if (auth()->check()) {
        if (auth()->user()->is_admin ?? false) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('customer.dashboard');
    }
    return redirect('/');
})->name('login.redirect');

/*
|--------------------------------------------------------------------------
| Routes Frontend
|--------------------------------------------------------------------------
*/

// Produits
Route::prefix('produits')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/vedettes', [ProductController::class, 'featured'])->name('featured');
    Route::get('/recherche', [ProductController::class, 'search'])->name('search');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('show');
});

// Catégories
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/{category:slug}', [CategoryController::class, 'show'])->name('show');

    // Routes AJAX
    Route::get('/api/filter', [CategoryController::class, 'filter'])->name('filter');
    Route::get('/api/search', [CategoryController::class, 'search'])->name('search');
});

// Panier
Route::prefix('panier')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/ajouter/{product}', [CartController::class, 'add'])->name('add');
    Route::patch('/modifier/{productId}', [CartController::class, 'update'])->name('update');
    Route::delete('/supprimer/{productId}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/vider', [CartController::class, 'clear'])->name('clear');
});

// Checkout
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
});

// Contact
Route::prefix('contact')->name('contact.')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('index');
    Route::post('/', [ContactController::class, 'store'])->name('store');

    // Route AJAX
    Route::get('/api/availability', [ContactController::class, 'checkAvailability'])->name('availability');
});

/*
|--------------------------------------------------------------------------
| Routes API
|--------------------------------------------------------------------------
*/

Route::get('/api/cart/count', function() {
    $cart = session()->get('cart', []);
    $count = array_sum(array_column($cart, 'quantity'));
    return response()->json(['count' => $count]);
})->name('api.cart.count');

/*
|--------------------------------------------------------------------------
| Routes Espace Client
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('mon-compte')->name('customer.')->group(function () {
    // Dashboard principal
    Route::get('/', [CustomerController::class, 'dashboard'])->name('dashboard');

    // Profil
    Route::get('/profil', [CustomerController::class, 'profile'])->name('profile');
    Route::put('/profil', [CustomerController::class, 'updateProfile'])->name('profile.update');
    Route::put('/mot-de-passe', [CustomerController::class, 'changePassword'])->name('password.change');

    // Commandes
    Route::get('/commandes', [CustomerController::class, 'orders'])->name('orders');
    Route::get('/commandes/{order}', [CustomerController::class, 'orderShow'])->name('orders.show');

    // Adresses
    Route::get('/adresses', [CustomerController::class, 'addresses'])->name('addresses');
    Route::put('/adresses', [CustomerController::class, 'updateAddresses'])->name('addresses.update');

    // Favoris
    Route::get('/favoris', [CustomerController::class, 'favorites'])->name('favorites');

    // Paramètres
    Route::get('/parametres', [CustomerController::class, 'settings'])->name('settings');
    Route::put('/parametres', [CustomerController::class, 'updateSettings'])->name('settings.update');
});

// Routes AJAX pour notifications client
Route::middleware(['auth'])->group(function () {
    Route::get('/customer/dashboard/chart-data', [CustomerController::class, 'getChartDataAjax'])->name('customer.dashboard.chart-data');
    Route::post('/customer/notifications/{id}/read', [CustomerController::class, 'markNotificationAsRead'])->name('customer.notifications.read');
    Route::post('/customer/notifications/read-all', [CustomerController::class, 'markAllNotificationsAsRead'])->name('customer.notifications.read-all');
});

/*
|--------------------------------------------------------------------------
| Routes Administration
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard admin
    Route::get('/', function() { return redirect()->route('admin.dashboard'); });
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [AdminDashboardController::class, 'stats'])->name('stats');

    // Gestion des produits
    Route::resource('products', AdminProductController::class);
    Route::patch('products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggleStatus');
    Route::patch('products/{product}/toggle', [AdminProductController::class, 'toggle'])->name('products.toggle');
    Route::post('products/{product}/duplicate', [AdminProductController::class, 'duplicate'])->name('products.duplicate');
    Route::get('products/stock/faible', [AdminProductController::class, 'lowStock'])->name('products.lowStock');
    Route::post('products/bulk-activate', [AdminProductController::class, 'bulkActivate'])->name('products.bulkActivate');
    Route::post('products/bulk-deactivate', [AdminProductController::class, 'bulkDeactivate'])->name('products.bulkDeactivate');
    Route::post('products/bulk-delete', [AdminProductController::class, 'bulkDelete'])->name('products.bulkDelete');
    Route::post('products/stock/update-bulk', [AdminProductController::class, 'updateBulkStock'])->name('products.updateBulkStock');
    Route::get('products/export', [AdminProductController::class, 'export'])->name('products.export');

    // Gestion des catégories
    Route::resource('categories', AdminCategoryController::class);
    Route::patch('categories/{category}/toggle', [AdminCategoryController::class, 'toggle'])->name('categories.toggle');
    Route::post('categories/{category}/generate-image', [AdminCategoryController::class, 'generateImage'])->name('categories.generate-image');
    Route::delete('categories/{category}/delete-image', [AdminCategoryController::class, 'deleteImage'])->name('categories.delete-image');

    // Gestion des commandes
    Route::resource('orders', AdminOrderController::class)->except(['create', 'store']);
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Gestion des utilisateurs/clients
    Route::get('/customers', [AdminUserController::class, 'index'])->name('customers.index');
    Route::get('/customers/{user}', [AdminUserController::class, 'show'])->name('customers.show');
    Route::resource('users', AdminUserController::class)->except(['create', 'store']);

    // Routes pour la génération d'images IA
    Route::prefix('image-generation')->name('image-generation.')->group(function () {
        Route::get('/', [ImageGenerationController::class, 'index'])->name('index');

        // Produits
        Route::post('/generate/{product}', [ImageGenerationController::class, 'generateSingle'])->name('generate-single');
        Route::post('/regenerate/{product}', [ImageGenerationController::class, 'regenerate'])->name('regenerate');
        Route::post('/batch', [ImageGenerationController::class, 'generateBatch'])->name('generate-batch');

        // Catégories
        Route::post('/generate-category/{category}', [ImageGenerationController::class, 'generateCategorySingle'])->name('generate-category-single');
        Route::post('/regenerate-category/{category}', [ImageGenerationController::class, 'regenerateCategory'])->name('regenerate-category');
        Route::post('/batch-categories', [ImageGenerationController::class, 'generateCategoryBatch'])->name('generate-category-batch');
        Route::delete('/delete-category/{category}', [ImageGenerationController::class, 'deleteCategoryImage'])->name('delete-category-image');
    });

    // Pages temporaires admin
    Route::get('/reviews', function() {
        return view('admin.reviews.index', ['reviews' => []]);
    })->name('reviews.index');

    Route::get('/reports', function() {
        return view('admin.reports.index');
    })->name('reports.index');
});

/*
|--------------------------------------------------------------------------
| Routes Paiement Stripe
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('payment')->name('payment.')->group(function () {
    // Page de paiement
    Route::get('/{order}', [PaymentController::class, 'show'])->name('show');

    // Traitement du paiement
    Route::post('/{order}/process', [PaymentController::class, 'process'])->name('process');

    // Pages de résultat
    Route::get('/{order}/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/{order}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
});

// Webhook Stripe (sans middleware auth)
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');

/*
|--------------------------------------------------------------------------
| Pages Statiques
|--------------------------------------------------------------------------
*/

Route::view('/conditions-generales', 'temp.terms')->name('terms');
Route::view('/politique-confidentialite', 'temp.privacy')->name('privacy');
Route::view('/mentions-legales', 'temp.legal')->name('legal');
Route::view('/livraison', 'temp.shipping')->name('shipping');
Route::view('/paiement', 'temp.payment')->name('payment');
