{{-- resources/views/payment/success.blade.php --}}
@extends('layouts.app')

@section('title', 'Paiement réussi')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="success-card">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>

                <h1 class="h3 mb-3">Paiement réussi !</h1>

                <p class="lead">
                    Votre commande #{{ $order->id }} a été confirmée et sera traitée dans les plus brefs délais.
                </p>

                <div class="order-details">
                    <div class="detail-item">
                        <strong>Montant payé :</strong>
                        <span class="text-success">{{ number_format($order->total_amount, 2) }}€</span>
                    </div>
                    <div class="detail-item">
                        <strong>Date de paiement :</strong>
                        <span>{{ $order->paid_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Méthode :</strong>
                        <span>Carte bancaire</span>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-primary me-3">
                        <i class="fas fa-eye me-2"></i>
                        Voir ma commande
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Continuer mes achats
                    </a>
                </div>

                <div class="email-info mt-4">
                    <p class="text-muted">
                        <i class="fas fa-envelope me-2"></i>
                        Un email de confirmation a été envoyé à {{ $order->user->email }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.success-card {
    background: white;
    padding: 3rem 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.success-icon {
    font-size: 4rem;
    color: #28a745;
    margin-bottom: 2rem;
}

.order-details {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    margin: 2rem 0;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.action-buttons {
    margin: 2rem 0;
}

.email-info {
    padding: 1rem;
    background: #e7f3ff;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}
</style>
@endpush
@endsection

{{-- resources/views/payment/cancel.blade.php --}}
@extends('layouts.app')

@section('title', 'Paiement annulé')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="cancel-card">
                <div class="cancel-icon">
                    <i class="fas fa-times-circle"></i>
                </div>

                <h1 class="h3 mb-3">Paiement annulé</h1>

                <p class="lead">
                    Le paiement de votre commande #{{ $order->id }} a été annulé.
                </p>

                <div class="order-info">
                    <p>Votre commande est toujours en attente de paiement.</p>
                    <p>Vous pouvez réessayer le paiement à tout moment.</p>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('payment.show', $order) }}" class="btn btn-primary me-3">
                        <i class="fas fa-credit-card me-2"></i>
                        Réessayer le paiement
                    </a>
                    <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-eye me-2"></i>
                        Voir la commande
                    </a>
                </div>

                <div class="help-info mt-4">
                    <p class="text-muted">
                        <i class="fas fa-question-circle me-2"></i>
                        Besoin d'aide ? <a href="{{ route('contact.index') }}">Contactez-nous</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.cancel-card {
    background: white;
    padding: 3rem 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.cancel-icon {
    font-size: 4rem;
    color: #dc3545;
    margin-bottom: 2rem;
}

.order-info {
    background: #fff3cd;
    padding: 1.5rem;
    border-radius: 10px;
    margin: 2rem 0;
    border-left: 4px solid #ffc107;
}

.help-info {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.help-info a {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
}

.help-info a:hover {
    text-decoration: underline;
}
</style>
@endpush
@endsection
