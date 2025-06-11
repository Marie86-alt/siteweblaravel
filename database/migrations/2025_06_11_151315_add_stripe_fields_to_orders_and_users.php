<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('delivery_instructions');
        });
        // Ajouter champs Stripe à la table orders
        Schema::table('orders', function (Blueprint $table) {
            $table->string('stripe_payment_intent_id')->nullable()->after('payment_status');
            $table->string('stripe_charge_id')->nullable()->after('stripe_payment_intent_id');
            $table->decimal('stripe_fee', 8, 2)->nullable()->after('stripe_charge_id');
            $table->timestamp('paid_at')->nullable()->after('stripe_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('stripe_customer_id');
        });
         Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_payment_intent_id',
                'stripe_charge_id',
                'stripe_fee',
                'paid_at'
            ]);
        });
    }
};
