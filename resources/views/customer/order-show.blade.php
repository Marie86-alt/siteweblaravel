@extends('customer.layout')

@section('customer-content')
<div class="customer-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="customer-title">
                <i class="fas fa-shopping-bag me-3"></i>
                Commande #{{ $order->id }}
            </h1>
            <p class="customer-subtitle">
                Passée le {{ $order->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>
        <div>
            <a href="{{ route('customer.orders') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour aux commandes
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Détails de la commande -->
    <div class="col-lg-8">
        <!-- Statut de la commande -->
        <div class="order-status-section">
            <h3>Statut de la commande</h3>
            <div class="status-timeline">
                <div class="status-step {{ in_array($order->status, ['pending', 'confirmed', 'preparing', 'shipped', 'delivered']) ? 'completed' : '' }}">
                    <div class="step-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="step-content">
                        <h5>Commande passée</h5>
                        <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <div class="status-step {{ in_array($order->status, ['confirmed', 'preparing', 'shipped', 'delivered']) ? 'completed' : ($order->status == 'pending' ? 'active' : '') }}">
                    <div class="step-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="step-content">
                        <h5>Commande confirmée</h5>
                        <span>En attente de confirmation</span>
                    </div>
                </div>

                <div class="status-step {{ in_array($order->status, ['preparing', 'shipped', 'delivered']) ? 'completed' : ($order->status == 'confirmed' ? 'active' : '') }}">
                    <div class="step-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="step-content">
                        <h5>Préparation</h5>
                        <span>Commande en préparation</span>
                    </div>
                </div>

                <div class="status-step {{ in_array($order->status, ['shipped', 'delivered']) ? 'completed' : ($order->status == 'preparing' ? 'active' : '') }}">
                    <div class="step-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="step-content">
                        <h5>Expédiée</h5>
                        <span>En cours de livraison</span>
                    </div>
                </div>

                <div class="status-step {{ $order->status == 'delivered' ? 'completed' : ($order->status == 'shipped' ? 'active' : '') }}">
                    <div class="step-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="step-content">
                        <h5>Livrée</h5>
                        <span>Commande livrée</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Articles commandés -->
        <div class="order-items-section">
            <h3>Articles commandés</h3>
            <div class="items-list">
                @foreach($order->orderItems as $item)
                <div class="order-item-detail">
                    <div class="item-image">
                        @if($item->product && $item->product->image)
                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}">
                        @else
                            <div class="no-image">
                                <i class="fas fa-apple-alt"></i>
                            </div>
                        @endif
                    </div>
                    <div class="item-info">
                        <h5>{{ $item->product_name }}</h5>
                        @if($item->product_sku)
                            <span class="sku">Réf: {{ $item->product_sku }}</span>
                        @endif
                        <div class="item-meta">
                            <span class="quantity">Quantité: {{ $item->quantity }}</span>
                            <span class="unit-price">Prix unitaire: {{ number_format($item->price, 2) }}€</span>
                        </div>
                    </div>
                    <div class="item-total">
                        <strong>{{ number_format($item->total, 2) }}€</strong>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Résumé et informations -->
    <div class="col-lg-4">
        <!-- Résumé de la commande -->
        <div class="order-summary">
            <h4>Résumé de la commande</h4>

            <div class="summary-line">
                <span>Sous-total :</span>
                <span>{{ number_format($order->subtotal ?? $order->total_amount, 2) }}€</span>
            </div>

            @if($order->shipping_amount)
            <div class="summary-line">
                <span>Frais de livraison :</span>
                <span>{{ number_format($order->shipping_amount, 2) }}€</span>
            </div>
            @endif

            <div class="summary-line total">
                <span><strong>Total :</strong></span>
                <span><strong>{{ number_format($order->total_amount, 2) }}€</strong></span>
            </div>

            <div class="payment-info">
                <h5>Informations de paiement</h5>
                <p><strong>Méthode :</strong> {{ $order->payment_method ?? 'Non spécifiée' }}</p>
                <p><strong>Statut :</strong>
                    <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                        {{ $order->payment_status == 'paid' ? 'Payé' : 'En attente' }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Adresses -->
        <div class="addresses-info">
            <h4>Informations de livraison</h4>

            @if($order->delivery_first_name)
            <div class="address-block">
                <h5>Adresse de livraison</h5>
                <p>
                    {{ $order->delivery_first_name }} {{ $order->delivery_last_name }}<br>
                    {{ $order->delivery_address }}<br>
                    {{ $order->delivery_postal_code }} {{ $order->delivery_city }}<br>
                    {{ $order->delivery_country }}
                </p>
            </div>
            @endif

            @if($order->billing_first_name)
            <div class="address-block">
                <h5>Adresse de facturation</h5>
                <p>
                    {{ $order->billing_first_name }} {{ $order->billing_last_name }}<br>
                    {{ $order->billing_email }}<br>
                    {{ $order->billing_phone }}
                </p>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="order-actions">
            @if($order->isCancellable())
            <button class="btn btn-outline-danger w-100 mb-2" onclick="cancelOrder()">
                <i class="fas fa-times me-2"></i>
                Annuler la commande
            </button>
            @endif

            <button class="btn btn-outline-primary w-100 mb-2">
                <i class="fas fa-download me-2"></i>
                Télécharger la facture
            </button>

            @if(in_array($order->status, ['delivered']))
            <button class="btn btn-outline-success w-100">
                <i class="fas fa-redo me-2"></i>
                Commander à nouveau
            </button>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .order-status-section, .order-items-section {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .order-status-section h3, .order-items-section h3 {
        margin-bottom: 2rem;
        color: #333;
        font-weight: 600;
    }

    .status-timeline {
        display: grid;
        gap: 1.5rem;
    }

    .status-step {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 10px;
        background: #f8f9fa;
        position: relative;
    }

    .status-step.completed {
        background: rgba(120, 230, 166, 0.1);
        border-left: 4px solid var(--primary-green);
    }

    .status-step.active {
        background: rgba(255, 193, 7, 0.1);
        border-left: 4px solid #ffc107;
    }

    .step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        flex-shrink: 0;
    }

    .status-step.completed .step-icon {
        background: var(--primary-green);
        color: white;
    }

    .status-step.active .step-icon {
        background: #ffc107;
        color: white;
    }

    .step-content h5 {
        margin: 0 0 0.5rem 0;
        font-weight: 600;
    }

    .step-content span {
        color: #666;
        font-size: 0.9rem;
    }

    .items-list {
        display: grid;
        gap: 1.5rem;
    }

    .order-item-detail {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .item-image {
        width: 80px;
        height: 80px;
        border-radius: 10px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-image {
        width: 100%;
        height: 100%;
        background: var(--light-green);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--dark-green);
        font-size: 2rem;
    }

    .item-info {
        flex: 1;
    }

    .item-info h5 {
        margin: 0 0 0.5rem 0;
        font-weight: 600;
    }

    .sku {
        color: #666;
        font-size: 0.9rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .item-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.9rem;
        color: #666;
    }

    .item-total {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--dark-green);
    }

    .order-summary, .addresses-info, .order-actions {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .order-summary h4, .addresses-info h4 {
        margin-bottom: 1.5rem;
        color: #333;
        font-weight: 600;
    }

    .summary-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
    }

    .summary-line.total {
        border-top: 2px solid #eee;
        padding-top: 1rem;
        margin-top: 1rem;
        font-size: 1.1rem;
    }

    .payment-info, .address-block {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #eee;
    }

    .payment-info h5, .address-block h5 {
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .address-block p {
        line-height: 1.6;
        margin: 0;
    }

    @media (max-width: 768px) {
        .customer-header .d-flex {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .order-item-detail {
            flex-direction: column;
            text-align: center;
        }

        .item-meta {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function cancelOrder() {
    if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
        // Logique d'annulation à implémenter
        alert('Fonctionnalité d\'annulation à implémenter');
    }
}
</script>
@endpush
@endsection
