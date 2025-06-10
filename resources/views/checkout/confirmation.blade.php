@extends('layouts.app')

@section('title', 'Commande confirmée - Fruits & Légumes Bio')
@section('description', 'Votre commande a été confirmée avec succès.')

@push('styles')
<style>
    .confirmation-container {
        background: #f8f9fa;
        min-height: 80vh;
        padding: 50px 0;
    }

    .confirmation-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        text-align: center;
        padding: 50px 30px;
    }

    .success-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
        color: white;
        font-size: 3rem;
        animation: bounce 1s ease-in-out;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }

    .order-details {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 30px;
        margin: 30px 0;
        text-align: left;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #dee2e6;
    }

    .detail-row:last-child {
        border-bottom: none;
        font-weight: bold;
        font-size: 1.2rem;
        color: var(--primary-green);
    }

    .order-items {
        margin: 20px 0;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .status-badge {
        background: #ffc107;
        color: #212529;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-group-custom {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }

    .btn-custom {
        padding: 12px 25px;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .btn-primary-custom {
        background: var(--primary-green);
        color: white;
        border: 2px solid var(--primary-green);
    }

    .btn-primary-custom:hover {
        background: var(--dark-green);
        border-color: var(--dark-green);
        transform: translateY(-2px);
        color: white;
    }

    .btn-secondary-custom {
        background: white;
        color: var(--primary-green);
        border: 2px solid var(--primary-green);
    }

    .btn-secondary-custom:hover {
        background: var(--primary-green);
        color: white;
        transform: translateY(-2px);
    }

    .address-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin: 20px 0;
    }

    .address-card {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 20px;
    }

    .address-title {
        font-weight: 600;
        color: var(--primary-green);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    @media (max-width: 768px) {
        .address-section {
            grid-template-columns: 1fr;
        }

        .btn-group-custom {
            flex-direction: column;
            align-items: center;
        }

        .confirmation-card {
            padding: 30px 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="confirmation-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="confirmation-card">
                    <!-- Icône de succès -->
                    <div class="success-icon">
                        <i class="fas fa-check"></i>
                    </div>

                    <!-- Message de confirmation -->
                    <h1 class="text-success mb-3">Commande confirmée !</h1>
                    <p class="lead text-muted mb-4">
                        Merci {{ $order->user->first_name }} ! Votre commande a été enregistrée avec succès.
                    </p>

                    <!-- Numéro de commande -->
                    <div class="alert alert-info d-inline-flex align-items-center gap-2">
                        <i class="fas fa-hashtag"></i>
                        <strong>Numéro de commande : {{ $order->order_number }}</strong>
                    </div>

                    <!-- Statut -->
                    <div class="my-4">
                        <span class="status-badge">
                            <i class="fas fa-clock"></i>
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <!-- Détails de la commande -->
                    <div class="order-details">
                        <h4 class="mb-3">
                            <i class="fas fa-receipt me-2"></i>
                            Détails de la commande
                        </h4>

                        <!-- Articles commandés -->
                        <div class="order-items">
                            <h6 class="mb-3">Articles commandés :</h6>
                            @foreach($order->orderItems as $item)
                                <div class="order-item">
                                    <div>
                                        <strong>{{ $item->product_name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $item->quantity }} × {{ number_format($item->price, 2, ',', ' ') }}€
                                        </small>
                                    </div>
                                    <div class="fw-bold text-success">
                                        {{ number_format($item->total, 2, ',', ' ') }}€
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Totaux -->
                        <div class="detail-row">
                            <span>Sous-total</span>
                            <span>{{ number_format($order->subtotal, 2, ',', ' ') }}€</span>
                        </div>

                        <div class="detail-row">
                            <span>Livraison</span>
                            <span>
                                @if($order->shipping_cost > 0)
                                    {{ number_format($order->shipping_cost, 2, ',', ' ') }}€
                                @else
                                    <span class="text-success">Gratuite</span>
                                @endif
                            </span>
                        </div>

                        <div class="detail-row">
                            <span>Total</span>
                            <span>{{ number_format($order->total_amount, 2, ',', ' ') }}€</span>
                        </div>
                    </div>

                    <!-- Adresses -->
                    <div class="address-section">
                        <!-- Adresse de facturation -->
                        <div class="address-card">
                            <div class="address-title">
                                <i class="fas fa-file-invoice"></i>
                                Adresse de facturation
                            </div>
                            <address class="mb-0">
                                {{ $order->billing_first_name }} {{ $order->billing_last_name }}<br>
                                {{ $order->billing_address }}<br>
                                {{ $order->billing_postal_code }} {{ $order->billing_city }}<br>
                                {{ $order->billing_country }}<br>
                                <strong>Email:</strong> {{ $order->billing_email }}<br>
                                <strong>Tél:</strong> {{ $order->billing_phone }}
                            </address>
                        </div>

                        <!-- Adresse de livraison -->
                        <div class="address-card">
                            <div class="address-title">
                                <i class="fas fa-shipping-fast"></i>
                                Adresse de livraison
                            </div>
                            <address class="mb-0">
                                {{ $order->delivery_first_name }} {{ $order->delivery_last_name }}<br>
                                {{ $order->delivery_address }}<br>
                                {{ $order->delivery_postal_code }} {{ $order->delivery_city }}<br>
                                {{ $order->delivery_country }}
                            </address>
                        </div>
                    </div>

                    <!-- Informations de paiement -->
                    <div class="alert alert-light">
                        <h6 class="mb-2">
                            <i class="fas fa-credit-card me-2"></i>
                            Méthode de paiement
                        </h6>
                        <p class="mb-0">
                            @switch($order->payment_method)
                                @case('card')
                                    <i class="fas fa-credit-card me-1"></i>
                                    Carte bancaire
                                    @break
                                @case('transfer')
                                    <i class="fas fa-university me-1"></i>
                                    Virement bancaire
                                    @break
                                @case('cash')
                                    <i class="fas fa-money-bill-wave me-1"></i>
                                    Paiement à la livraison
                                    @break
                            @endswitch
                        </p>
                        <small class="text-muted">
                            Statut: {{ ucfirst($order->payment_status) }}
                        </small>
                    </div>

                    <!-- Notes -->
                    @if($order->notes)
                        <div class="alert alert-secondary">
                            <h6 class="mb-2">
                                <i class="fas fa-sticky-note me-2"></i>
                                Notes de commande
                            </h6>
                            <p class="mb-0">{{ $order->notes }}</p>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="btn-group-custom">
                        <a href="{{ route('home') }}" class="btn-custom btn-primary-custom">
                            <i class="fas fa-home"></i>
                            Retour à l'accueil
                        </a>
                        <a href="{{ route('products.index') }}" class="btn-custom btn-secondary-custom">
                            <i class="fas fa-shopping-bag"></i>
                            Continuer mes achats
                        </a>
                    </div>

                    <!-- Informations de suivi -->
                    <div class="alert alert-info mt-4">
                        <h6 class="mb-2">
                            <i class="fas fa-info-circle me-2"></i>
                            Que se passe-t-il maintenant ?
                        </h6>
                        <ul class="list-unstyled mb-0 text-start">
                            <li><i class="fas fa-check text-success me-2"></i>Vous recevrez un email de confirmation</li>
                            <li><i class="fas fa-clock text-warning me-2"></i>Nous préparons votre commande (24-48h)</li>
                            <li><i class="fas fa-truck text-info me-2"></i>Expédition et livraison (2-3 jours ouvrés)</li>
                            <li><i class="fas fa-star text-primary me-2"></i>Profitez de vos produits frais !</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
