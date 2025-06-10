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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['fixed', 'percentage']); // réduction fixe ou pourcentage
            $table->decimal('value', 10, 2); // montant ou pourcentage
            $table->decimal('minimum_amount', 10, 2)->nullable(); // montant minimum de commande
            $table->integer('usage_limit')->nullable(); // nombre d'utilisations max
            $table->integer('used_count')->default(0);
            $table->integer('usage_limit_per_user')->nullable(); // limite par utilisateur
            $table->boolean('is_active')->default(true);
            $table->datetime('starts_at')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
