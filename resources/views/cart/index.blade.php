@extends('layouts.app')

@section('title', 'Mon Panier - Fruits & Légumes Bio')
@section('description', 'Finalisez votre commande de fruits et légumes frais. Livraison rapide dans toute la région.')

@push('styles')
<style>
    .cart-container {
        background: #f8f9fa;
        min-height: 60vh;
        padding: 30px 0;
    }

    .cart-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
    }

    .cart-header {
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        color: white;
        padding: 25px;
        margin: 0;
    }

    .cart-item {
        padding: 20px;
        border-bottom: 1px solid #eee;
        transition: all 0.3s;
    }

    .cart-item:hover {
        background: #f8f9fa;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .item-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--light-green);
    }

    .item-details h6 {
        color: #333;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .item-price {
        color: var(--primary-green);
        font-weight: bold;
        font-size: 1.1rem;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8f9fa;
        border-radius: 25px;
        padding: 5px;
        width: fit-content;
    }

    .quantity-btn {
        background: white;
        border: 1px solid #ddd;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        color: var(--primary-green);
        font-weight: bold;
    }

    .quantity-btn:hover {
        background: var(--primary-green);
        color: white;
        border-color: var(--primary-green);
    }

    .quantity-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .quantity-display {
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 8px 15px;
        font-weight: bold;
        min-width: 50px;
        text-align: center;
    }

    .remove-btn {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 5px;
        border-radius: 5px;
        transition: all 0.2s;
    }

    .remove-btn:hover {
        background: #dc3545;
        color: white;
    }

    .cart-summary {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        padding: 25px;
        position: sticky;
        top: 100px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .summary-row:last-child {
        border-bottom: none;
        font-weight: bold;
        font-size: 1.2rem;
        color: var(--primary-green);
        padding-top: 15px;
    }

    .btn-checkout {
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        border: none;
        color: white;
        padding: 15px;
        border-radius: 25px;
        font-size: 1.1rem;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s;
        margin-top: 20px;
    }

    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(39, 174, 96, 0.3);
        color: white;
    }

    .btn-checkout:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .empty-cart {
        text-align: center;
        padding: 60px 30px;
        color: #666;
    }

    .empty-cart-icon {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 20px;
    }

    .continue-shopping {
        background: var(--primary-green);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
    }

    .continue-shopping:hover {
        background: var(--dark-green);
        color: white;
        transform: translateY(-2px);
    }

    .delivery-info {
        background: #e8f5e8;
        border: 1px solid #c3e6c3;
        border-radius: 10px;
        padding: 15px;
        margin-top: 20px;
        text-align: center;
    }

    .promo-section {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 10px;
        padding: 15px;
        margin-top: 15px;
    }

    .promo-input {
        border-radius: 20px;
        border: 1px solid #ddd;
        padding: 10px 15px;
    }

    .promo-btn {
        background: var(--orange);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 20px;
        margin-left: 10px;
    }

    @media (max-width: 768px) {
        .cart-item {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .quantity-controls {
            justify-content: center;
        }

        .cart-summary {
            position: static;
            margin-top: 30px;
        }
    }
</style>
@endpush

@section('content')
<div class="cart-container">
    <div class="container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produits</a></li>
                        <li class="breadcrumb-item active">Mon Panier</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if(count($cartItems) > 0)
            <div class="row">
                <!-- Articles du panier -->
                <div class="col-lg-8 mb-4">
                    <div class="cart-card">
                        <div class="cart-header">
                            <h2 class="mb-0">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Mon Panier ({{ count($cartItems) }} article{{ count($cartItems) > 1 ? 's' : '' }})
                            </h2>
                        </div>

                        @foreach($cartItems as $item)
                            <div class="cart-item">
                                <div class="row align-items-center">
                                    <!-- Image produit -->
                                    <div class="col-md-2 col-3 mb-3 mb-md-0">
                                        @if($item['product']->images && count($item['product']->images) > 0)
                                            <img src="{{ asset('storage/products/' . $item['product']->images[0]) }}"
                                                 alt="{{ $item['product']->name }}" class="item-image">
                                        @else
                                            <div class="item-image">
                                                @switch($item['product']->category->name ?? 'default')
                                                    @case('Fruits')
                                                    @case('Fruits rouges')
                                                    @case('Agrumes')
                                                        🍎
                                                        @break
                                                    @case('Légumes')
                                                    @case('Légumes verts')
                                                    @case('Légumes racines')
                                                        🥕
                                                        @break
                                                    @case('Herbes aromatiques')
                                                        🌿
                                                        @break
                                                    @default
                                                        🥗
                                                @endswitch
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Détails produit -->
                                    <div class="col-md-4 col-9 mb-3 mb-md-0">
                                        <div class="item-details">
                                            <h6>{{ $item['product']->name }}</h6>
                                            <p class="text-muted mb-1">{{ $item['product']->category->name ?? '' }}</p>
                                            <div class="item-price">{{ number_format($item['product']->price, 2, ',', ' ') }}€ / {{ $item['product']->unit }}</div>
                                            @if($item['product']->is_bio)
                                                <span class="badge bg-success mt-1">BIO</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Contrôles quantité -->
                                    <div class="col-md-3 col-8 mb-3 mb-md-0">
                                        <div class="quantity-controls">
                                            <button type="button" class="quantity-btn decrease-btn"
                                                    onclick="updateQuantity({{ $item['product']->id }}, {{ $item['quantity'] - 1 }})"
                                                    {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                                −
                                            </button>
                                            <span class="quantity-display">{{ $item['quantity'] }}</span>
                                            <button type="button" class="quantity-btn increase-btn"
                                                    onclick="updateQuantity({{ $item['product']->id }}, {{ $item['quantity'] + 1 }})"
                                                    {{ $item['quantity'] >= $item['product']->stock_quantity ? 'disabled' : '' }}>
                                                +
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Prix total et suppression -->
                                    <div class="col-md-2 col-4">
                                        <div class="text-end">
                                            <div class="fw-bold text-success mb-2">{{ number_format($item['subtotal'], 2, ',', ' ') }}€</div>
                                            <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="remove-btn" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Actions panier -->
                        <div class="cart-item border-0">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('products.index') }}" class="continue-shopping">
                                        <i class="fas fa-arrow-left"></i>
                                        Continuer mes achats
                                    </a>
                                </div>
                                <div class="col-md-6 text-end mt-3 mt-md-0">
                                    <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger"
                                                onclick="return confirm('Êtes-vous sûr de vouloir vider votre panier ?')">
                                            <i class="fas fa-trash me-2"></i>
                                            Vider le panier
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Résumé commande -->
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4 class="mb-3">
                            <i class="fas fa-receipt me-2"></i>
                            Résumé de la commande
                        </h4>

                        <div class="summary-row">
                            <span>Sous-total ({{ array_sum(array_column($cartItems, 'quantity')) }} articles)</span>
                            <span>{{ number_format($total, 2, ',', ' ') }}€</span>
                        </div>

                        <div class="summary-row">
                            <span>Livraison</span>
                            <span class="{{ $total >= 50 ? 'text-success' : '' }}">
                                {{ $total >= 50 ? 'Gratuite' : '5,90€' }}
                            </span>
                        </div>

                        @php
                            $shipping = $total >= 50 ? 0 : 5.90;
                            $totalWithShipping = $total + $shipping;
                        @endphp

                        <div class="summary-row">
                            <span>Total</span>
                            <span>{{ number_format($totalWithShipping, 2, ',', ' ') }}€</span>
                        </div>

                        <button type="button" class="btn-checkout" onclick="proceedToCheckout()">
                            <i class="fas fa-credit-card me-2"></i>
                            Procéder au paiement
                        </button>

                        <!-- Info livraison -->
                        <div class="delivery-info">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-truck text-success me-2"></i>
                                <small>
                                    @if($total >= 50)
                                        <strong>Livraison gratuite !</strong>
                                    @else
                                        Plus que {{ number_format(50 - $total, 2, ',', ' ') }}€ pour la livraison gratuite
                                    @endif
                                </small>
                            </div>
                        </div>

                        <!-- Section promo -->
                        <div class="promo-section">
                            <h6 class="mb-3">
                                <i class="fas fa-tag me-2"></i>
                                Code promo
                            </h6>
                            <form class="d-flex">
                                <input type="text" class="form-control promo-input" placeholder="Code promo">
                                <button type="submit" class="promo-btn">Appliquer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Panier vide -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="cart-card">
                        <div class="empty-cart">
                            <div class="empty-cart-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3>Votre panier est vide</h3>
                            <p class="text-muted mb-4">Découvrez notre sélection de fruits et légumes frais et ajoutez vos produits préférés à votre panier.</p>
                            <a href="{{ route('products.index') }}" class="continue-shopping">
                                <i class="fas fa-shopping-basket"></i>
                                Découvrir nos produits
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
     // Mise à jour de la quantité
    function updateQuantity(productId, newQuantity) {
        if (newQuantity < 1) return;

        // Créer le formulaire et l'envoyer
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/panier/modifier/${productId}`;

        // Token CSRF
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfInput);

        // Method PATCH
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';
        form.appendChild(methodInput);

        // Quantité
        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = 'quantity';
        quantityInput.value = newQuantity;
        form.appendChild(quantityInput);

        document.body.appendChild(form);
        form.submit();
    }

    // Procéder au checkout
    function proceedToCheckout() {
        @auth
            window.location.href = "{{ route('checkout.index') }}";

        @else
            if (confirm('Vous devez être connecté pour finaliser votre commande. Souhaitez-vous vous connecter maintenant ?')) {
                window.location.href = "{{ route('login') }}";
            }
        @endauth
    }

    // Confirmation avant suppression
    document.querySelectorAll('.remove-btn').forEach(button => {
        button.closest('form').addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
