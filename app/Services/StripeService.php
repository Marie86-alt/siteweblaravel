<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\SetupIntent;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.stripe.secret'));
        Stripe::setApiVersion(config('stripe.api_version'));
    }

    /**
     * Créer un PaymentIntent pour une commande
     */
    public function createPaymentIntent(Order $order, array $options = [])
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $this->convertToStripeAmount($order->total_amount),
                'currency' => config('stripe.currency'),
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'user_email' => $order->user->email,
                ],
                'description' => "Commande #{$order->id} - Fruits & Légumes",
                'receipt_email' => $order->user->email,
                'setup_future_usage' => 'off_session', // Pour sauvegarder la carte
            ] + $options);

            // Sauvegarder l'ID du PaymentIntent dans la commande
            $order->update([
                'stripe_payment_intent_id' => $paymentIntent->id,
                'payment_status' => 'pending'
            ]);

            return $paymentIntent;

        } catch (\Exception $e) {
            Log::error('Erreur création PaymentIntent Stripe: ' . $e->getMessage());
            throw new \Exception('Erreur lors de la création du paiement: ' . $e->getMessage());
        }
    }

    /**
     * Créer ou récupérer un client Stripe
     */
    public function createOrGetCustomer(User $user)
    {
        try {
            if ($user->stripe_customer_id) {
                return Customer::retrieve($user->stripe_customer_id);
            }

            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->full_name ?: $user->name,
                'phone' => $user->phone,
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);

            // Sauvegarder l'ID du customer
            $user->update(['stripe_customer_id' => $customer->id]);

            return $customer;

        } catch (\Exception $e) {
            Log::error('Erreur création customer Stripe: ' . $e->getMessage());
            throw new \Exception('Erreur lors de la création du client: ' . $e->getMessage());
        }
    }

    /**
     * Confirmer un paiement
     */
    public function confirmPayment($paymentIntentId)
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (\Exception $e) {
            Log::error('Erreur confirmation paiement Stripe: ' . $e->getMessage());
            throw new \Exception('Erreur lors de la confirmation du paiement: ' . $e->getMessage());
        }
    }

    /**
     * Rembourser un paiement
     */
    public function refundPayment($paymentIntentId, $amount = null)
    {
        try {
            $refundData = ['payment_intent' => $paymentIntentId];

            if ($amount) {
                $refundData['amount'] = $this->convertToStripeAmount($amount);
            }

            return \Stripe\Refund::create($refundData);

        } catch (\Exception $e) {
            Log::error('Erreur remboursement Stripe: ' . $e->getMessage());
            throw new \Exception('Erreur lors du remboursement: ' . $e->getMessage());
        }
    }

    /**
     * Convertir le montant pour Stripe (centimes)
     */
    private function convertToStripeAmount($amount)
    {
        return (int) ($amount * 100); // Convertir en centimes
    }

    /**
     * Convertir le montant depuis Stripe
     */
    public function convertFromStripeAmount($amount)
    {
        return $amount / 100;
    }

    /**
     * Vérifier la signature du webhook
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        try {
            return \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                config('stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            Log::error('Erreur vérification webhook Stripe: ' . $e->getMessage());
            throw new \Exception('Signature webhook invalide');
        }
    }
}
