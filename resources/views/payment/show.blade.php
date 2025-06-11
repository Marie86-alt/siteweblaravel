@extends('layouts.app')

@section('title', 'Paiement - Commande #' . $order->id)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- En-tête -->
            <div class="payment-header text-center mb-4">
                <h1 class="h3">
                    <i class="fas fa-credit-card me-2"></i>
                    Paiement sécurisé
                </h1>
                <p class="text-muted">Commande #{{ $order->id }} • {{ number_format($order->total_amount, 2) }}€</p>
            </div>

            <div class="row">
                <!-- Formulaire de paiement -->
                <div class="col-lg-7">
                    <div class="payment-form">
                        <h4 class="mb-3">
                            <i class="fas fa-lock me-2 text-success"></i>
                            Informations de paiement
                        </h4>

                        <div class="stripe-badges mb-3">
                            <img src="https://js.stripe.com/v3/fingerprinted/img/visa-729c05c240c4.svg" alt="Visa" height="24">
                            <img src="https://js.stripe.com/v3/fingerprinted/img/mastercard-4d8844094130.svg" alt="Mastercard" height="24">
                            <img src="https://js.stripe.com/v3/fingerprinted/img/amex-a49b82f46c5c.svg" alt="Amex" height="24">
                        </div>

                        <form id="payment-form">
                            @csrf

                            <!-- Stripe Elements Container -->
                            <div class="form-group mb-3">
                                <label class="form-label">Numéro de carte</label>
                                <div id="card-number-element" class="stripe-element">
                                    <!-- Stripe Elements injectera l'input ici -->
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Date d'expiration</label>
                                        <div id="card-expiry-element" class="stripe-element">
                                            <!-- Stripe Elements -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Code CVC</label>
                                        <div id="card-cvc-element" class="stripe-element">
                                            <!-- Stripe Elements -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Nom sur la carte -->
                            <div class="form-group mb-3">
                                <label class="form-label">Nom sur la carte</label>
                                <input type="text" class="form-control" id="cardholder-name"
                                       value="{{ $order->user->full_name ?: $order->user->name }}" required>
                            </div>

                            <!-- Messages d'erreur -->
                            <div id="card-errors" class="alert alert-danger d-none" role="alert"></div>

                            <!-- Bouton de paiement -->
                            <button type="submit" id="submit-payment" class="btn btn-success btn-lg w-100" disabled>
                                <span id="button-text">
                                    <i class="fas fa-lock me-2"></i>
                                    Payer {{ number_format($order->total_amount, 2) }}€
                                </span>
                                <div id="spinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                    <span class="visually-hidden">Traitement...</span>
                                </div>
                            </button>

                            <!-- Sécurité info -->
                            <div class="security-info mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Paiement sécurisé par Stripe. Vos informations bancaires ne sont jamais stockées sur nos serveurs.
                                </small>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Résumé de commande -->
                <div class="col-lg-5">
                    <div class="order-summary">
                        <h4 class="mb-3">Résumé de la commande</h4>

                        <!-- Articles -->
                        <div class="order-items">
                            @foreach($order->orderItems as $item)
                            <div class="order-item">
                                <div class="item-info">
                                    <h6>{{ $item->product_name }}</h6>
                                    <span class="text-muted">{{ $item->quantity }} × {{ number_format($item->price, 2) }}€</span>
                                </div>
                                <div class="item-total">
                                    {{ number_format($item->total, 2) }}€
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Totaux -->
                        <div class="order-totals">
                            <div class="total-line">
                                <span>Sous-total :</span>
                                <span>{{ number_format($order->total_amount, 2) }}€</span>
                            </div>
                            <div class="total-line">
                                <span>Livraison :</span>
                                <span class="text-success">Gratuite</span>
                            </div>
                            <div class="total-line total">
                                <strong>
                                    <span>Total :</span>
                                    <span>{{ number_format($order->total_amount, 2) }}€</span>
                                </strong>
                            </div>
                        </div>

                        <!-- Adresse de livraison -->
                        <div class="delivery-info mt-4">
                            <h6>Livraison à :</h6>
                            <address class="mb-0">
                                {{ $order->delivery_first_name }} {{ $order->delivery_last_name }}<br>
                                {{ $order->delivery_address }}<br>
                                {{ $order->delivery_postal_code }} {{ $order->delivery_city }}
                            </address>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>

<script>
// Configuration Stripe
const stripe = Stripe('{{ $stripePublicKey }}');
const elements = stripe.elements();

// Style des éléments Stripe
const style = {
    base: {
        color: '#32325d',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
            color: '#aab7c4'
        }
    },
    invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
    }
};

// Créer les éléments Stripe
const cardNumber = elements.create('cardNumber', {style: style});
const cardExpiry = elements.create('cardExpiry', {style: style});
const cardCvc = elements.create('cardCvc', {style: style});

// Monter les éléments
cardNumber.mount('#card-number-element');
cardExpiry.mount('#card-expiry-element');
cardCvc.mount('#card-cvc-element');

// Gérer les erreurs en temps réel
cardNumber.on('change', handleChange);
cardExpiry.on('change', handleChange);
cardCvc.on('change', handleChange);

function handleChange(event) {
    const displayError = document.getElementById('card-errors');
    const submitButton = document.getElementById('submit-payment');

    if (event.error) {
        displayError.textContent = event.error.message;
        displayError.classList.remove('d-none');
        submitButton.disabled = true;
    } else {
        displayError.classList.add('d-none');
        submitButton.disabled = false;
    }
}

// Gérer la soumission du formulaire
const form = document.getElementById('payment-form');
form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const submitButton = document.getElementById('submit-payment');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');

    // Désactiver le bouton et afficher le spinner
    submitButton.disabled = true;
    buttonText.classList.add('d-none');
    spinner.classList.remove('d-none');

    // Confirmer le paiement avec Stripe
    const {error} = await stripe.confirmCardPayment('{{ $paymentIntent->client_secret }}', {
        payment_method: {
            card: cardNumber,
            billing_details: {
                name: document.getElementById('cardholder-name').value,
                email: '{{ $order->user->email }}',
            }
        }
    });

    if (error) {
        // Afficher l'erreur
        const errorElement = document.getElementById('card-errors');
        errorElement.textContent = error.message;
        errorElement.classList.remove('d-none');

        // Réactiver le bouton
        submitButton.disabled = false;
        buttonText.classList.remove('d-none');
        spinner.classList.add('d-none');
    } else {
        // Paiement réussi - confirmer côté serveur
        fetch('{{ route("payment.process", $order) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                payment_method_id: 'confirmed'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                throw new Error(data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = 'Erreur lors du traitement du paiement.';
            errorElement.classList.remove('d-none');

            // Réactiver le bouton
            submitButton.disabled = false;
            buttonText.classList.remove('d-none');
            spinner.classList.add('d-none');
        });
    }
});
</script>

@push('styles')
<style>
.payment-form {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.order-summary {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 15px;
    position: sticky;
    top: 2rem;
}

.stripe-element {
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    background: white;
    transition: border-color 0.3s ease;
}

.stripe-element:focus-within {
    border-color: #28a745;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
}

.stripe-badges img {
    margin-right: 8px;
    opacity: 0.7;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #dee2e6;
}

.order-item:last-child {
    border-bottom: none;
}

.order-totals {
    border-top: 2px solid #dee2e6;
    padding-top: 1rem;
    margin-top: 1rem;
}

.total-line {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.total-line.total {
    font-size: 1.2rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #dee2e6;
}

.security-info {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.delivery-info {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.payment-header {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .payment-form, .order-summary {
        padding: 1.5rem;
    }

    .order-summary {
        position: static;
        margin-top: 2rem;
    }
}
</style>
@endpush
@endsection
