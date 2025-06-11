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
            // Ajouter company après birth_date
            $table->string('company')->nullable()->after('birth_date');

            // Adresses de facturation
            $table->text('billing_address')->nullable()->after('company');
            $table->string('billing_city')->nullable()->after('billing_address');
            $table->string('billing_postal_code', 10)->nullable()->after('billing_city');
            $table->string('billing_country')->default('France')->after('billing_postal_code');

            // Adresses de livraison
            $table->text('delivery_address')->nullable()->after('billing_country');
            $table->string('delivery_city')->nullable()->after('delivery_address');
            $table->string('delivery_postal_code', 10)->nullable()->after('delivery_city');
            $table->string('delivery_country')->default('France')->after('delivery_postal_code');
            $table->text('delivery_instructions')->nullable()->after('delivery_country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Vérifier si les colonnes existent avant de les supprimer
            if (Schema::hasColumn('users', 'company')) {
                $table->dropColumn('company');
            }
            if (Schema::hasColumn('users', 'billing_address')) {
                $table->dropColumn('billing_address');
            }
            if (Schema::hasColumn('users', 'billing_city')) {
                $table->dropColumn('billing_city');
            }
            if (Schema::hasColumn('users', 'billing_postal_code')) {
                $table->dropColumn('billing_postal_code');
            }
            if (Schema::hasColumn('users', 'billing_country')) {
                $table->dropColumn('billing_country');
            }
            if (Schema::hasColumn('users', 'delivery_address')) {
                $table->dropColumn('delivery_address');
            }
            if (Schema::hasColumn('users', 'delivery_city')) {
                $table->dropColumn('delivery_city');
            }
            if (Schema::hasColumn('users', 'delivery_postal_code')) {
                $table->dropColumn('delivery_postal_code');
            }
            if (Schema::hasColumn('users', 'delivery_country')) {
                $table->dropColumn('delivery_country');
            }
            if (Schema::hasColumn('users', 'delivery_instructions')) {
                $table->dropColumn('delivery_instructions');
            }
        });
    }
};
