@extends('layouts.app')

@section('title', 'Finaliser ma commande - Fruits & Légumes Bio')
@section('description', 'Finalisez votre commande de fruits et légumes frais.')

@push('styles')
<style>
    .checkout-container {
        background: #f8f9fa;
        min-height: 80vh;
        padding: 30px 0;
    }

    .checkout-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
        margin-bottom: 30px;
    }

    .checkout-header {
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        color: white;
        padding: 25px;
        margin: 0;
    }

    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
        gap: 20px;
    }

    .step {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        background: white;
        border-radius: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .step.active {
        background: var(--primary-green);
        color: white;
    }

    .form-section {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .section-title {
        color: var(--primary-green);
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 12px 15px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(39, 174, 96, 0.25);
    }

    .order-summary {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        position: sticky;
        top: 100px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }

    .summary-item:last-child {
        border-bottom: none;
        font-weight: bold;
        font-size: 1.2rem;
        color: var(--primary-green);
    }

    .product-mini {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .product-mini img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 5px;
    }

    .btn-place-order {
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

    .btn-place-order:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(39, 174, 96, 0.3);
        color: white;
    }

    .btn-place-order.stripe-mode {
        background: linear-gradient(135deg, #635bff, #4f46e5);
    }

    .btn-place-order.stripe-mode:hover {
        box-shadow: 0 8px 25px rgba(99, 91, 255, 0.3);
    }

    .payment-method {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .payment-method:hover {
        border-color: #28a745;
        box-shadow: 0 2px 10px rgba(40, 167, 69, 0.1);
    }

    .payment-method.selected {
        border-color: #28a745;
        background: rgba(40, 167, 69, 0.05);
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
    }

    .payment-method input[type="radio"] {
        transform: scale(1.2);
        accent-color: #28a745;
    }

    .card-brands img {
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }

    .payment-method.selected .card-brands img {
        opacity: 1;
    }

    .stripe-info {
        background: rgba(0, 123, 255, 0.05);
        border-left: 3px solid #007bff;
        padding: 10px 15px;
        border-radius: 0 8px 8px 0;
    }

    .payment-additional-info {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .payment-additional-info.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .checkbox-custom {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 20px 0;
    }

    .delivery-info {
        background: #e8f5e8;
        border: 1px solid #c3e6c3;
        border-radius: 10px;
        padding: 15px;
        margin-top: 15px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .step-indicator {
            flex-wrap: wrap;
            gap: 10px;
        }

        .step {
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        .order-summary {
            position: static;
            margin-top: 2rem;
        }

        .form-section {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="checkout-container">
    <div class="container">
        <!-- Indicateur d'étapes -->
        <div class="step-indicator">
            <div class="step">
                <i class="fas fa-shopping-cart"></i>
                <span>Panier</span>
            </div>
            <div class="step active">
                <i class="fas fa-truck"></i>
                <span>Livraison</span>
            </div>
            <div class="step">
                <i class="fas fa-credit-card"></i>
                <span>Paiement</span>
            </div>
            <div class="step">
                <i class="fas fa-check"></i>
                <span>Confirmation</span>
            </div>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
            @csrf
            <div class="row">
                <!-- Formulaire de commande -->
                <div class="col-lg-8">

                    <!-- Informations de facturation -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-user"></i>
                            Informations de facturation
                        </h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="billing_first_name" class="form-label">Prénom *</label>
                                    <input type="text" class="form-control @error('billing_first_name') is-invalid @enderror"
                                           id="billing_first_name" name="billing_first_name"
                                           value="{{ old('billing_first_name', $user->first_name) }}" required>
                                    @error('billing_first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="billing_last_name" class="form-label">Nom *</label>
                                    <input type="text" class="form-control @error('billing_last_name') is-invalid @enderror"
                                           id="billing_last_name" name="billing_last_name"
                                           value="{{ old('billing_last_name', $user->last_name) }}" required>
                                    @error('billing_last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="billing_email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('billing_email') is-invalid @enderror"
                                           id="billing_email" name="billing_email"
                                           value="{{ old('billing_email', $user->email) }}" required>
                                    @error('billing_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="billing_phone" class="form-label">Téléphone *</label>
                                    <input type="tel" class="form-control @error('billing_phone') is-invalid @enderror"
                                           id="billing_phone" name="billing_phone"
                                           value="{{ old('billing_phone', $user->phone) }}" required>
                                    @error('billing_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="billing_address" class="form-label">Adresse *</label>
                            <input type="text" class="form-control @error('billing_address') is-invalid @enderror"
                                   id="billing_address" name="billing_address"
                                   value="{{ old('billing_address', $user->billing_address) }}" required>
                            @error('billing_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="billing_city" class="form-label">Ville *</label>
                                    <input type="text" class="form-control @error('billing_city') is-invalid @enderror"
                                           id="billing_city" name="billing_city"
                                           value="{{ old('billing_city', $user->billing_city) }}" required>
                                    @error('billing_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="billing_postal_code" class="form-label">Code postal *</label>
                                    <input type="text" class="form-control @error('billing_postal_code') is-invalid @enderror"
                                           id="billing_postal_code" name="billing_postal_code"
                                           value="{{ old('billing_postal_code', $user->billing_postal_code) }}" required>
                                    @error('billing_postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="billing_country" class="form-label">Pays *</label>
                                    <select class="form-control @error('billing_country') is-invalid @enderror"
                                            id="billing_country" name="billing_country" required>
                                        <option value="France" {{ old('billing_country', $user->billing_country ?? 'France') == 'France' ? 'selected' : '' }}>France</option>
                                        <option value="Belgique" {{ old('billing_country', $user->billing_country) == 'Belgique' ? 'selected' : '' }}>Belgique</option>
                                        <option value="Suisse" {{ old('billing_country', $user->billing_country) == 'Suisse' ? 'selected' : '' }}>Suisse</option>
                                    </select>
                                    @error('billing_country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Adresse de livraison -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-shipping-fast"></i>
                            Adresse de livraison
                        </h3>

                        <div class="checkbox-custom">
                            <input type="checkbox" id="delivery_same_as_billing" name="delivery_same_as_billing"
                                   value="1" {{ old('delivery_same_as_billing', true) ? 'checked' : '' }}>
                            <label for="delivery_same_as_billing">Livrer à la même adresse que la facturation</label>
                        </div>

                        <div id="delivery-fields" style="{{ old('delivery_same_as_billing', true) ? 'display: none;' : '' }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="delivery_first_name" class="form-label">Prénom</label>
                                        <input type="text" class="form-control @error('delivery_first_name') is-invalid @enderror"
                                               id="delivery_first_name" name="delivery_first_name"
                                               value="{{ old('delivery_first_name') }}">
                                        @error('delivery_first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="delivery_last_name" class="form-label">Nom</label>
                                        <input type="text" class="form-control @error('delivery_last_name') is-invalid @enderror"
                                               id="delivery_last_name" name="delivery_last_name"
                                               value="{{ old('delivery_last_name') }}">
                                        @error('delivery_last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="delivery_address" class="form-label">Adresse</label>
                                <input type="text" class="form-control @error('delivery_address') is-invalid @enderror"
                                    id="delivery_address" name="delivery_address"
                                    value="{{ old('delivery_address') }}">
                                @error('delivery_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="delivery_city" class="form-label">Ville</label>
                                        <input type="text" class="form-control @error('delivery_city') is-invalid @enderror"
                                            id="delivery_city" name="delivery_city"
                                            value="{{ old('delivery_city') }}">
                                        @error('delivery_city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="delivery_postal_code" class="form-label">Code postal</label>
                                        <input type="text" class="form-control @error('delivery_postal_code') is-invalid @enderror"
                                            id="delivery_postal_code" name="delivery_postal_code"
                                            value="{{ old('delivery_postal_code') }}">
                                        @error('delivery_postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="delivery_country" class="form-label">Pays</label>
                                        <select class="form-control @error('delivery_country') is-invalid @enderror"
                                                id="delivery_country" name="delivery_country">
                                            <option value="France" {{ old('delivery_country', 'France') == 'France' ? 'selected' : '' }}>France</option>
                                            <option value="Belgique" {{ old('delivery_country') == 'Belgique' ? 'selected' : '' }}>Belgique</option>
                                            <option value="Suisse" {{ old('delivery_country') == 'Suisse' ? 'selected' : '' }}>Suisse</option>
                                        </select>
                                        @error('delivery_country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-3">
                                <button type="button" id="copy-address-btn" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-copy me-1"></i>Copier l'adresse de facturation
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Méthode de paiement -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-credit-card"></i>
                            Méthode de paiement
                        </h3>

                        {{-- Option Stripe (Carte bancaire) --}}
                        <div class="payment-method" data-method="stripe">
                            <div class="d-flex align-items-center">
                                <input type="radio" id="payment_stripe" name="payment_method" value="stripe"
                                       {{ old('payment_method', 'stripe') == 'stripe' ? 'checked' : '' }}>
                                <label for="payment_stripe" class="ms-3 flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fab fa-cc-stripe me-2 text-primary" style="font-size: 1.2rem;"></i>
                                            <strong>Carte bancaire</strong>
                                            <br><small class="text-muted">Paiement sécurisé par Stripe</small>
                                        </div>
                                        <div class="card-brands">
                                            <img src="https://js.stripe.com/v3/fingerprinted/img/visa-729c05c240c4.svg" alt="Visa" height="20" style="margin-right: 5px;">
                                            <img src="https://js.stripe.com/v3/fingerprinted/img/mastercard-4d8844094130.svg" alt="Mastercard" height="20" style="margin-right: 5px;">
                                            <img src="https://js.stripe.com/v3/fingerprinted/img/amex-a49b82f46c5c.svg" alt="Amex" height="20">
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="stripe-info mt-2" style="padding-left: 2rem;">
                                <small class="text-success">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Paiement 100% sécurisé • Vos données bancaires ne sont jamais stockées
                                </small>
                            </div>
                        </div>

                        {{-- Option Virement --}}
                        <div class="payment-method" data-method="transfer">
                            <div class="d-flex align-items-center">
                                <input type="radio" id="payment_transfer" name="payment_method" value="transfer"
                                       {{ old('payment_method') == 'transfer' ? 'checked' : '' }}>
                                <label for="payment_transfer" class="ms-3 flex-grow-1">
                                    <i class="fas fa-university me-2 text-info"></i>
                                    <strong>Virement bancaire</strong>
                                    <br><small class="text-muted">Paiement par virement (3-5 jours ouvrés)</small>
                                </label>
                            </div>
                        </div>

                        {{-- Option Paiement à la livraison --}}
                        <div class="payment-method" data-method="cash">
                            <div class="d-flex align-items-center">
                                <input type="radio" id="payment_cash" name="payment_method" value="cash"
                                       {{ old('payment_method') == 'cash' ? 'checked' : '' }}>
                                <label for="payment_cash" class="ms-3 flex-grow-1">
                                    <i class="fas fa-money-bill-wave me-2 text-warning"></i>
                                    <strong>Paiement à la livraison</strong>
                                    <br><small class="text-muted">Espèces ou chèque à la réception</small>
                                </label>
                            </div>
                        </div>

                        @error('payment_method')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror

                        {{-- Info supplémentaire selon le mode sélectionné --}}
                        <div id="payment-info" class="mt-3" style="display: none;">
                            <div id="stripe-info" class="payment-additional-info">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div>
                                        <strong>Paiement par carte :</strong><br>
                                        Vous serez redirigé vers notre page de paiement sécurisée pour saisir vos informations bancaires.
                                    </div>
                                </div>
                            </div>

                            <div id="transfer-info" class="payment-additional-info">
                                <div class="alert alert-warning d-flex align-items-center">
                                    <i class="fas fa-clock me-2"></i>
                                    <div>
                                        <strong>Virement bancaire :</strong><br>
                                        Vos coordonnées bancaires vous seront envoyées par email après validation de la commande.
                                        <br><small>Délai de traitement : 3-5 jours ouvrés après réception du virement.</small>
                                    </div>
                                </div>
                            </div>

                            <div id="cash-info" class="payment-additional-info">
                                <div class="alert alert-success d-flex align-items-center">
                                    <i class="fas fa-truck me-2"></i>
                                    <div>
                                        <strong>Paiement à la livraison :</strong><br>
                                        Réglez directement au livreur en espèces ou par chèque.
                                        <br><small>Préparez l'appoint si possible pour faciliter la livraison.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes additionnelles -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-sticky-note"></i>
                            Instructions de livraison (optionnel)
                        </h3>

                        <div class="form-group">
                            <textarea class="form-control @error('delivery_instructions') is-invalid @enderror"
                                      id="delivery_instructions" name="delivery_instructions" rows="3"
                                      placeholder="Code d'accès, digicode, étage, instructions spéciales...">{{ old('delivery_instructions') }}</textarea>
                            @error('delivery_instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes" class="form-label">Notes de commande</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3"
                                      placeholder="Commentaires additionnels...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Résumé de commande -->
                <div class="col-lg-4">
                    <div class="order-summary">
                        <h4 class="mb-3">
                            <i class="fas fa-receipt me-2"></i>
                            Résumé de la commande
                        </h4>

                        <!-- Produits -->
                        <div class="mb-3">
                            @foreach($cartItems as $item)
                                <div class="product-mini">
                                    <div class="product-mini-image">
                                        @if($item->product->images && count($item->product->images) > 0)
                                            <img src="{{ asset('storage/products/' . $item->product->images[0]) }}"
                                                 alt="{{ $item->product->name }}">
                                        @else
                                            <div style="width: 40px; height: 40px; background: #f8f9fa; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                                🥗
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ $item->product->name }}</div>
                                        <small class="text-muted">{{ $item->quantity }} × {{ number_format($item->product->price, 2, ',', ' ') }}€</small>
                                    </div>
                                    <div class="fw-bold text-success">
                                        {{ number_format($item->quantity * $item->product->price, 2, ',', ' ') }}€
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Totaux -->
                        <div class="summary-item">
                            @php
                                $totalQuantity = 0;
                                foreach($cartItems as $item) {
                                    $totalQuantity += $item->quantity;
                                }
                            @endphp
                            <span>Sous-total ({{ $totalQuantity }} articles)</span>
                            <span>{{ number_format($subtotal, 2, ',', ' ') }}€</span>
                        </div>

                        <div class="summary-item">
                            <span>Livraison</span>
                            <span class="{{ $shipping == 0 ? 'text-success' : '' }}">
                                {{ $shipping == 0 ? 'Gratuite' : number_format($shipping, 2, ',', ' ') . '€' }}
                            </span>
                        </div>

                        <div class="summary-item">
                            <span>Total</span>
                            <span>{{ number_format($total, 2, ',', ' ') }}€</span>
                        </div>

                        <!-- Bouton de commande -->
                        <button type="submit" class="btn-place-order" id="place-order-btn">
                            <i class="fas fa-credit-card me-2"></i>
                            Payer par carte sécurisée
                        </button>

                        <!-- Info livraison -->
                        <div class="delivery-info">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-truck text-success me-2"></i>
                                <small>
                                    @if($shipping == 0)
                                        <strong>Livraison gratuite !</strong>
                                    @else
                                        Livraison: {{ number_format($shipping, 2, ',', ' ') }}€
                                    @endif
                                </small>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Livraison estimée: 2-3 jours ouvrés
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'adresse de livraison
    const deliverySameCheckbox = document.getElementById('delivery_same_as_billing');
    const deliveryFields = document.getElementById('delivery-fields');
    const copyAddressBtn = document.getElementById('copy-address-btn');

    deliverySameCheckbox.addEventListener('change', function() {
        if (this.checked) {
            deliveryFields.style.display = 'none';
            // Vider les champs de livraison
            deliveryFields.querySelectorAll('input, select').forEach(field => {
                field.value = '';
                field.removeAttribute('required');
            });
        } else {
            deliveryFields.style.display = 'block';
            // Rendre les champs obligatoires
            deliveryFields.querySelectorAll('input[name^="delivery_"], select[name^="delivery_"]').forEach(field => {
                if (!field.name.includes('country')) {
                    field.setAttribute('required', 'required');
                }
            });
        }
    });

    // Copier l'adresse de facturation vers livraison
    copyAddressBtn.addEventListener('click', function() {
        if (deliverySameCheckbox.checked) {
            return; // Ne pas copier si les champs sont cachés
        }

        const billingFields = {
            'billing_first_name': 'delivery_first_name',
            'billing_last_name': 'delivery_last_name',
            'billing_address': 'delivery_address',
            'billing_city': 'delivery_city',
            'billing_postal_code': 'delivery_postal_code',
            'billing_country': 'delivery_country'
        };

        Object.entries(billingFields).forEach(([billing, delivery]) => {
            const billingField = document.getElementById(billing);
            const deliveryField = document.getElementById(delivery);
            if (billingField && deliveryField) {
                deliveryField.value = billingField.value;
            }
        });

        // Animation de confirmation
        copyAddressBtn.innerHTML = '<i class="fas fa-check me-1"></i>Adresse copiée !';
        copyAddressBtn.classList.add('btn-success');
        copyAddressBtn.classList.remove('btn-outline-secondary');

        setTimeout(() => {
            copyAddressBtn.innerHTML = '<i class="fas fa-copy me-1"></i>Copier l\'adresse de facturation';
            copyAddressBtn.classList.remove('btn-success');
            copyAddressBtn.classList.add('btn-outline-secondary');
        }, 2000);
    });

    // Gestion des méthodes de paiement avec infos
    const paymentMethods = document.querySelectorAll('.payment-method');
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const paymentInfo = document.getElementById('payment-info');
    const submitBtn = document.getElementById('place-order-btn');

    function updatePaymentInfo(method) {
        // Masquer toutes les infos
        document.querySelectorAll('.payment-additional-info').forEach(info => {
            info.classList.remove('active');
        });

        // Afficher l'info correspondante
        const targetInfo = document.getElementById(method + '-info');
        if (targetInfo) {
            paymentInfo.style.display = 'block';
            targetInfo.classList.add('active');
        } else {
            paymentInfo.style.display = 'none';
        }

        // Changer le style du bouton selon la méthode
        submitBtn.className = 'btn-place-order';
        if (method === 'stripe') {
            submitBtn.classList.add('stripe-mode');
            submitBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i>Payer par carte sécurisée';
        } else if (method === 'transfer') {
            submitBtn.innerHTML = '<i class="fas fa-university me-2"></i>Confirmer la commande (virement)';
        } else if (method === 'cash') {
            submitBtn.innerHTML = '<i class="fas fa-money-bill-wave me-2"></i>Commander (paiement livraison)';
        } else {
            submitBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Finaliser la commande';
        }
    }

    // Gestion des clics sur les méthodes de paiement
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;

                // Retirer la classe selected de tous
                paymentMethods.forEach(m => m.classList.remove('selected'));
                // Ajouter à celui cliqué
                this.classList.add('selected');

                // Mettre à jour les infos
                updatePaymentInfo(radio.value);
            }
        });
    });

    // Marquer la méthode sélectionnée au chargement et afficher les infos
    paymentRadios.forEach(radio => {
        if (radio.checked) {
            radio.closest('.payment-method').classList.add('selected');
            updatePaymentInfo(radio.value);
        }
    });

    // Validation du formulaire
    const form = document.getElementById('checkout-form');
    form.addEventListener('submit', function(e) {
        const selectedPayment = document.querySelector('input[name="payment_method"]:checked');

        // Désactiver le bouton et changer le texte
        submitBtn.disabled = true;

        if (selectedPayment && selectedPayment.value === 'stripe') {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Redirection vers le paiement...';
        } else {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement en cours...';
        }

        // Validation basique côté client
        let isValid = true;
        const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        // Si l'adresse de livraison est différente, vérifier les champs requis
        if (!deliverySameCheckbox.checked) {
            const deliveryRequiredFields = [
                'delivery_first_name', 'delivery_last_name',
                'delivery_address', 'delivery_city', 'delivery_postal_code'
            ];

            deliveryRequiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field && !field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else if (field) {
                    field.classList.remove('is-invalid');
                }
            });
        }

        if (!isValid) {
            e.preventDefault();
            submitBtn.disabled = false;
            updatePaymentInfo(selectedPayment.value);

            // Scroll vers le premier champ invalide
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
        }
    });

    // Auto-complétion intelligente
    const billingFields = ['first_name', 'last_name', 'email', 'phone'];
    billingFields.forEach(field => {
        const billingField = document.getElementById('billing_' + field);
        if (billingField) {
            billingField.addEventListener('blur', function() {
                // Si l'adresse de livraison est visible et le champ correspondant est vide
                if (!deliverySameCheckbox.checked) {
                    const deliveryField = document.getElementById('delivery_' + field);
                    if (deliveryField && !deliveryField.value) {
                        deliveryField.value = this.value;
                    }
                }
            });
        }
    });

    // Validation en temps réel
    const allInputs = form.querySelectorAll('input, select, textarea');
    allInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });

    // Formatage automatique du code postal
    const postalCodeFields = document.querySelectorAll('input[name$="_postal_code"]');
    postalCodeFields.forEach(field => {
        field.addEventListener('input', function() {
            // Supprimer tout ce qui n'est pas un chiffre
            this.value = this.value.replace(/\D/g, '');

            // Limiter à 5 chiffres pour la France
            if (this.value.length > 5) {
                this.value = this.value.substring(0, 5);
            }
        });
    });

    // Formatage automatique du téléphone
    const phoneField = document.getElementById('billing_phone');
    if (phoneField) {
        phoneField.addEventListener('input', function() {
            // Permettre les chiffres, espaces, points, tirets et plus
            this.value = this.value.replace(/[^\d\s\.\-\+\(\)]/g, '');
        });
    }

    // Animation smooth pour les sections
    const sections = document.querySelectorAll('.form-section');
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    sections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(section);
    });
});
</script>
@endpush
