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

    .payment-method {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .payment-method:hover {
        border-color: var(--primary-green);
    }

    .payment-method.selected {
        border-color: var(--primary-green);
        background: rgba(39, 174, 96, 0.1);
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
                                   value="{{ old('billing_address') }}" required>
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
                                           value="{{ old('billing_city') }}" required>
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
                                           value="{{ old('billing_postal_code') }}" required>
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
                                        <option value="France" {{ old('billing_country') == 'France' ? 'selected' : '' }}>France</option>
                                        <option value="Belgique" {{ old('billing_country') == 'Belgique' ? 'selected' : '' }}>Belgique</option>
                                        <option value="Suisse" {{ old('billing_country') == 'Suisse' ? 'selected' : '' }}>Suisse</option>
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
                                            <option value="France" {{ old('delivery_country') == 'France' ? 'selected' : '' }}>France</option>
                                            <option value="Belgique" {{ old('delivery_country') == 'Belgique' ? 'selected' : '' }}>Belgique</option>
                                            <option value="Suisse" {{ old('delivery_country') == 'Suisse' ? 'selected' : '' }}>Suisse</option>
                                        </select>
                                        @error('delivery_country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Méthode de paiement -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-credit-card"></i>
                            Méthode de paiement
                        </h3>

                        <div class="payment-method" data-method="card">
                            <div class="d-flex align-items-center">
                                <input type="radio" id="payment_card" name="payment_method" value="card"
                                       {{ old('payment_method', 'card') == 'card' ? 'checked' : '' }}>
                                <label for="payment_card" class="ms-3 flex-grow-1">
                                    <i class="fas fa-credit-card me-2"></i>
                                    <strong>Carte bancaire</strong>
                                    <br><small class="text-muted">Paiement sécurisé par carte</small>
                                </label>
                            </div>
                        </div>

                        <div class="payment-method" data-method="transfer">
                            <div class="d-flex align-items-center">
                                <input type="radio" id="payment_transfer" name="payment_method" value="transfer"
                                       {{ old('payment_method') == 'transfer' ? 'checked' : '' }}>
                                <label for="payment_transfer" class="ms-3 flex-grow-1">
                                    <i class="fas fa-university me-2"></i>
                                    <strong>Virement bancaire</strong>
                                    <br><small class="text-muted">Paiement par virement (3-5 jours)</small>
                                </label>
                            </div>
                        </div>

                        <div class="payment-method" data-method="cash">
                            <div class="d-flex align-items-center">
                                <input type="radio" id="payment_cash" name="payment_method" value="cash"
                                       {{ old('payment_method') == 'cash' ? 'checked' : '' }}>
                                <label for="payment_cash" class="ms-3 flex-grow-1">
                                    <i class="fas fa-money-bill-wave me-2"></i>
                                    <strong>Paiement à la livraison</strong>
                                    <br><small class="text-muted">Espèces ou chèque à la réception</small>
                                </label>
                            </div>
                        </div>

                        @error('payment_method')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes additionnelles -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-sticky-note"></i>
                            Notes de commande (optionnel)
                        </h3>

                        <div class="form-group">
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="4"
                                      placeholder="Instructions de livraison, commentaires...">{{ old('notes') }}</textarea>
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
                            <i class="fas fa-lock me-2"></i>
                            Finaliser la commande
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

        // Gestion des méthodes de paiement
        const paymentMethods = document.querySelectorAll('.payment-method');
        const paymentRadios = document.querySelectorAll('input[name="payment_method"]');

        paymentMethods.forEach(method => {
            method.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;

                // Retirer la classe selected de tous
                paymentMethods.forEach(m => m.classList.remove('selected'));
                // Ajouter à celui cliqué
                this.classList.add('selected');
            });
        });

        // Marquer la méthode sélectionnée au chargement
        paymentRadios.forEach(radio => {
            if (radio.checked) {
                radio.closest('.payment-method').classList.add('selected');
            }
        });

        // Validation du formulaire
        const form = document.getElementById('checkout-form');
        const submitBtn = document.getElementById('place-order-btn');

        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement en cours...';
        });

        // Copier les informations de facturation vers livraison
        function copyBillingToDelivery() {
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
        }

        // Bouton pour copier l'adresse
        const copyAddressBtn = document.createElement('button');
        copyAddressBtn.type = 'button';
        copyAddressBtn.className = 'btn btn-outline-secondary btn-sm mt-2';
        copyAddressBtn.innerHTML = '<i class="fas fa-copy me-1"></i>Copier l\'adresse de facturation';
        copyAddressBtn.addEventListener('click', copyBillingToDelivery);

        if (deliveryFields.querySelector('.row')) {
            deliveryFields.querySelector('.row').appendChild(copyAddressBtn);
        }
    });
</script>
@endpush
