@extends('customer.layout')

@section('customer-content')
<div class="customer-header">
    <h1 class="customer-title">
        <i class="fas fa-shopping-bag me-3"></i>
        Mes Commandes
    </h1>
    <p class="customer-subtitle">
        Consultez l'historique de toutes vos commandes et suivez leur statut.
    </p>
</div>

@if($orders->count() > 0)
    <div class="orders-container">
        @foreach($orders as $order)
        <div class="order-card">
            <div class="order-header">
                <div class="order-info">
                    <h5 class="order-number">Commande #{{ $order->id }}</h5>
                    <span class="order-date">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                <div class="order-status">
                    <span class="badge status-{{ $order->status }}">
                        {{ $order->formatted_status }}
                    </span>
                </div>
            </div>

            <div class="order-content">
                <div class="order-items">
                    @foreach($order->orderItems->take(3) as $item)
                    <div class="order-item">
                        <div class="item-image">
                            @if($item->product && $item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}">
                            @else
                                <div class="no-image">
                                    <i class="fas fa-apple-alt"></i>
                                </div>
                            @endif
                        </div>
                        <div class="item-details">
                            <h6>{{ $item->product_name }}</h6>
                            <span class="quantity">Quantité : {{ $item->quantity }}</span>
                            <span class="price">{{ number_format($item->price, 2) }}€</span>
                        </div>
                    </div>
                    @endforeach

                    @if($order->orderItems->count() > 3)
                    <div class="more-items">
                        <span>+ {{ $order->orderItems->count() - 3 }} autre(s) article(s)</span>
                    </div>
                    @endif
                </div>

                <div class="order-summary">
                    <div class="summary-row">
                        <span>Articles :</span>
                        <span>{{ $order->orderItems->count() }}</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total :</span>
                        <span><strong>{{ number_format($order->total_amount, 2) }}€</strong></span>
                    </div>
                </div>
            </div>

            <div class="order-actions">
                <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-outline-primary">
                    <i class="fas fa-eye"></i> Voir détails
                </a>

                @if($order->isCancellable())
                <button class="btn btn-outline-danger" onclick="cancelOrder({{ $order->id }})">
                    <i class="fas fa-times"></i> Annuler
                </button>
                @endif

                @if(in_array($order->status, ['delivered', 'completed']))
                <a href="#" class="btn btn-outline-success">
                    <i class="fas fa-redo"></i> Commander à nouveau
                </a>
                @endif
            </div>
        </div>
        @endforeach

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-shopping-bag fa-4x text-muted mb-4"></i>
        <h4>Aucune commande</h4>
        <p class="text-muted mb-4">Vous n'avez pas encore passé de commande.</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary">
            <i class="fas fa-shopping-cart me-2"></i>
            Découvrir nos produits
        </a>
    </div>
@endif

@push('styles')
<style>
    .orders-container {
        display: grid;
        gap: 2rem;
    }

    .order-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .order-card:hover {
        transform: translateY(-5px);
    }

    .order-header {
        background: #f8f9fa;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #eee;
    }

    .order-number {
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
        color: #333;
    }

    .order-date {
        color: #666;
        font-size: 0.9rem;
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-confirmed, .status-preparing {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-shipped {
        background: #d4edda;
        color: #155724;
    }

    .status-delivered, .status-completed {
        background: #d1e7dd;
        color: #0f5132;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .order-content {
        padding: 1.5rem;
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }

    .order-items {
        display: grid;
        gap: 1rem;
    }

    .order-item {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .item-image {
        width: 50px;
        height: 50px;
        border-radius: 8px;
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
    }

    .item-details h6 {
        margin: 0 0 0.5rem 0;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .quantity, .price {
        display: block;
        font-size: 0.85rem;
        color: #666;
    }

    .price {
        font-weight: 600;
        color: var(--dark-green);
    }

    .more-items {
        text-align: center;
        color: #666;
        font-style: italic;
        font-size: 0.9rem;
    }

    .order-summary {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        height: fit-content;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .summary-row.total {
        border-top: 1px solid #ddd;
        padding-top: 0.5rem;
        margin-top: 1rem;
        font-size: 1.1rem;
    }

    .order-actions {
        padding: 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #eee;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    @media (max-width: 768px) {
        .order-content {
            grid-template-columns: 1fr;
        }

        .order-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .order-actions {
            flex-direction: column;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function cancelOrder(orderId) {
    if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
        // Logique d'annulation à implémenter
        alert('Fonctionnalité d\'annulation à implémenter');
    }
}
</script>
@endpush
@endsection
