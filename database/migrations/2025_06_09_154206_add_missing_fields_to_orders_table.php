<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Vérifiez d'abord si les colonnes n'existent pas déjà
            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->after('status');
            }
            if (!Schema::hasColumn('orders', 'shipping_cost')) {
                $table->decimal('shipping_cost', 8, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('orders', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->after('shipping_cost');
            }

            // Champs de facturation (vérifiez aussi)
            if (!Schema::hasColumn('orders', 'billing_first_name')) {
                $table->string('billing_first_name')->after('payment_status');
                $table->string('billing_last_name');
                $table->string('billing_email');
                $table->string('billing_phone');
                $table->string('billing_address');
                $table->string('billing_city');
                $table->string('billing_postal_code');
                $table->string('billing_country');

                // Champs de livraison
                $table->string('delivery_first_name');
                $table->string('delivery_last_name');
                $table->string('delivery_address');
                $table->string('delivery_city');
                $table->string('delivery_postal_code');
                $table->string('delivery_country');

                $table->text('notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Pas de rollback pour éviter les erreurs
    }
};
