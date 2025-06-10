<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;

use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentification Laravel
Auth::routes();

//route checkout
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
});


/*
|--------------------------------------------------------------------------
| Routes Frontend
|--------------------------------------------------------------------------
*/

// Produits (routes réelles)
Route::prefix('produits')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/vedettes', [ProductController::class, 'featured'])->name('featured');
    Route::get('/recherche', [ProductController::class, 'search'])->name('search');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('show');
});


/*
|--------------------------------------------------------------------------
| Routes Catégories
|--------------------------------------------------------------------------
*/

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [App\Http\Controllers\CategoryController::class, 'index'])->name('index');
    Route::get('/{category:slug}', [App\Http\Controllers\CategoryController::class, 'show'])->name('show');

    // Routes AJAX
    Route::get('/api/filter', [App\Http\Controllers\CategoryController::class, 'filter'])->name('filter');
    Route::get('/api/search', [App\Http\Controllers\CategoryController::class, 'search'])->name('search');
});

// Panier reelles
Route::prefix('panier')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/ajouter/{product}', [CartController::class, 'add'])->name('add');
    Route::patch('/modifier/{productId}', [CartController::class, 'update'])->name('update');
    Route::delete('/supprimer/{productId}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/vider', [CartController::class, 'clear'])->name('clear');
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
| Routes Contact
|--------------------------------------------------------------------------
*/

Route::prefix('contact')->name('contact.')->group(function () {
    Route::get('/', [App\Http\Controllers\ContactController::class, 'index'])->name('index');
    Route::post('/', [App\Http\Controllers\ContactController::class, 'store'])->name('store');

    // Route AJAX
    Route::get('/api/availability', [App\Http\Controllers\ContactController::class, 'checkAvailability'])->name('availability');
});

/*
|--------------------------------------------------------------------------
| Routes Administration
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard admin
    Route::get('/', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [App\Http\Controllers\Admin\AdminDashboardController::class, 'stats'])->name('stats');

    // Gestion des produits
    Route::resource('products', App\Http\Controllers\Admin\AdminProductController::class);
    Route::patch('products/{product}/toggle-status', [App\Http\Controllers\Admin\AdminProductController::class, 'toggleStatus'])->name('products.toggleStatus');
    Route::get('products/stock/faible', [App\Http\Controllers\Admin\AdminProductController::class, 'lowStock'])->name('products.lowStock');
    Route::post('products/bulk-activate', [App\Http\Controllers\Admin\AdminProductController::class, 'bulkActivate'])->name('products.bulkActivate');
    Route::post('products/bulk-deactivate', [App\Http\Controllers\Admin\AdminProductController::class, 'bulkDeactivate'])->name('products.bulkDeactivate');
    Route::post('products/bulk-delete', [App\Http\Controllers\Admin\AdminProductController::class, 'bulkDelete'])->name('products.bulkDelete');
    Route::post('products/stock/update-bulk', [App\Http\Controllers\Admin\AdminProductController::class, 'updateBulkStock'])->name('products.updateBulkStock');
    Route::get('products/export', [App\Http\Controllers\Admin\AdminProductController::class, 'export'])->name('products.export');

      // Routes pour la génération d'images IA
    Route::prefix('image-generation')->name('image-generation.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ImageGenerationController::class, 'index'])
            ->name('index');
        Route::post('/generate/{product}', [App\Http\Controllers\Admin\ImageGenerationController::class, 'generateSingle'])
            ->name('generate-single');
        Route::post('/regenerate/{product}', [App\Http\Controllers\Admin\ImageGenerationController::class, 'regenerate'])
            ->name('regenerate');
        Route::post('/batch', [App\Http\Controllers\Admin\ImageGenerationController::class, 'generateBatch'])
            ->name('generate-batch');
    });

    // Gestion des catégories
    Route::resource('categories', App\Http\Controllers\Admin\AdminCategoryController::class);

    // Gestion des commandes
    Route::resource('orders', App\Http\Controllers\Admin\AdminOrderController::class)->except(['create', 'store']);
    Route::patch('orders/{order}/status', [App\Http\Controllers\Admin\AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Gestion des utilisateurs
    Route::resource('users', App\Http\Controllers\Admin\AdminUserController::class)->except(['create', 'store']);
});

/*
|--------------------------------------------------------------------------
| Pages Statiques temporaires
|--------------------------------------------------------------------------
*/

Route::view('/conditions-generales', 'temp.terms')->name('terms');
Route::view('/politique-confidentialite', 'temp.privacy')->name('privacy');
Route::view('/mentions-legales', 'temp.legal')->name('legal');
Route::view('/livraison', 'temp.shipping')->name('shipping');
Route::view('/paiement', 'temp.payment')->name('payment');
