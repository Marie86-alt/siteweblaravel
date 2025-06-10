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
        Schema::table('orders', function (Blueprint $table) {
            // Champs de base
            $table->decimal('subtotal', 10, 2)->after('status');
            $table->decimal('shipping_cost', 8, 2)->default(0)->after('subtotal');
            $table->decimal('total_amount', 10, 2)->after('shipping_cost');

            // Champs de facturation
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

            // Champs additionnels
            $table->text('notes')->nullable();
            $table->string('order_number')->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal', 'shipping_cost', 'total_amount',
                'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone',
                'billing_address', 'billing_city', 'billing_postal_code', 'billing_country',
                'delivery_first_name', 'delivery_last_name', 'delivery_address',
                'delivery_city', 'delivery_postal_code', 'delivery_country',
                'notes', 'order_number'
            ]);
        });
    }
};
