<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\StripeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PaymentController extends Controller
{
     protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Afficher la page de paiement
     */
    public function show(Order $order)
    {
        // Vérifier que la commande appartient à l'utilisateur
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }

        // Vérifier que la commande est en attente de paiement
        if ($order->payment_status !== 'pending') {
            return redirect()->route('customer.orders.show', $order)
                           ->with('error', 'Cette commande a déjà été payée ou annulée.');
        }

        try {
            // Créer ou récupérer le PaymentIntent
            if (!$order->stripe_payment_intent_id) {
                $paymentIntent = $this->stripeService->createPaymentIntent($order);
            } else {
                $paymentIntent = $this->stripeService->retrievePaymentIntent($order->stripe_payment_intent_id);
            }

            return view('payment.show', [
                'order' => $order,
                'paymentIntent' => $paymentIntent,
                'stripePublicKey' => config('stripe.public_key')
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur affichage paiement: ' . $e->getMessage());
            return redirect()->route('customer.orders.show', $order)
                           ->with('error', 'Erreur lors de la préparation du paiement.');
        }
    }

    /**
     * Traiter le paiement
     */
    public function process(Request $request, Order $order)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        if ($order->user_id !== Auth::id()) {
            abort(404);
        }

        DB::beginTransaction();

        try {
            // Confirmer le paiement avec Stripe
            $paymentIntent = $this->stripeService->confirmPayment($order->stripe_payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {
                // Mettre à jour la commande
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'stripe_charge_id' => $paymentIntent->charges->data[0]->id ?? null,
                    'stripe_fee' => isset($paymentIntent->charges->data[0])
                        ? $this->stripeService->convertFromStripeAmount($paymentIntent->charges->data[0]->application_fee_amount ?? 0)
                        : 0,
                    'paid_at' => now(),
                ]);

                DB::commit();

                // Envoyer notification de confirmation
                \App\Http\Controllers\CustomerController::sendOrderConfirmation($order);

                return response()->json([
                    'success' => true,
                    'redirect' => route('payment.success', $order)
                ]);
            } else {
                throw new \Exception('Paiement non confirmé par Stripe');
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur traitement paiement: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du traitement du paiement: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Page de succès
     */
    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }

        if ($order->payment_status !== 'paid') {
            return redirect()->route('customer.orders.show', $order);
        }

        return view('payment.success', compact('order'));
    }

    /**
     * Page d'annulation
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }

        return view('payment.cancel', compact('order'));
    }

    /**
     * Webhook Stripe
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = $this->stripeService->verifyWebhookSignature($payload, $signature);

            Log::info('Webhook Stripe reçu: ' . $event->type);

            // Traiter différents types d'événements
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;

                case 'charge.dispute.created':
                    $this->handleChargeDispute($event->data->object);
                    break;

                default:
                    Log::info('Type d\'événement non géré: ' . $event->type);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Erreur webhook Stripe: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook error'], 400);
        }
    }

    /**
     * Gérer un paiement réussi
     */
    private function handlePaymentSucceeded($paymentIntent)
    {
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order && $order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'paid_at' => now(),
            ]);

            Log::info("Commande #{$order->id} marquée comme payée via webhook");
        }
    }

    /**
     * Gérer un paiement échoué
     */
    private function handlePaymentFailed($paymentIntent)
    {
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'failed',
                'status' => 'cancelled',
            ]);

            Log::warning("Paiement échoué pour la commande #{$order->id}");
        }
    }

    /**
     * Gérer une contestation
     */
    private function handleChargeDispute($dispute)
    {
        Log::warning('Contestation reçue: ' . $dispute->id);
        // Ici vous pouvez notifier l'admin, etc.
    }
}

