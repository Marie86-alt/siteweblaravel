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
            // Vérifier et ajouter seulement les colonnes manquantes

            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }

            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            if (!Schema::hasColumn('users', 'company')) {
                $table->string('company')->nullable()->after('birth_date');
            }

            // Adresse de facturation
            if (!Schema::hasColumn('users', 'billing_address')) {
                $table->text('billing_address')->nullable()->after('company');
            }

            if (!Schema::hasColumn('users', 'billing_city')) {
                $table->string('billing_city')->nullable()->after('billing_address');
            }

            if (!Schema::hasColumn('users', 'billing_postal_code')) {
                $table->string('billing_postal_code', 10)->nullable()->after('billing_city');
            }

            if (!Schema::hasColumn('users', 'billing_country')) {
                $table->string('billing_country')->default('France')->after('billing_postal_code');
            }

            // Adresse de livraison
            if (!Schema::hasColumn('users', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('billing_country');
            }

            if (!Schema::hasColumn('users', 'delivery_city')) {
                $table->string('delivery_city')->nullable()->after('delivery_address');
            }

            if (!Schema::hasColumn('users', 'delivery_postal_code')) {
                $table->string('delivery_postal_code', 10)->nullable()->after('delivery_city');
            }

            if (!Schema::hasColumn('users', 'delivery_country')) {
                $table->string('delivery_country')->default('France')->after('delivery_postal_code');
            }

            if (!Schema::hasColumn('users', 'delivery_instructions')) {
                $table->text('delivery_instructions')->nullable()->after('delivery_country');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'company',
                'billing_address',
                'billing_city',
                'billing_postal_code',
                'billing_country',
                'delivery_address',
                'delivery_city',
                'delivery_postal_code',
                'delivery_country',
            ]);
        });
    }
};
