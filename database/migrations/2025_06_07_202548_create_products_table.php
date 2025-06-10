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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable(); // prix barré
            $table->string('unit')->default('kg'); // kg, pièce, botte, etc.
            $table->decimal('weight', 8, 3)->nullable(); // poids unitaire
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock')->default(5); // seuil d'alerte
            $table->json('images')->nullable(); // array d'images
            $table->boolean('is_bio')->default(false);
            $table->boolean('is_featured')->default(false); // produit vedette
            $table->boolean('is_active')->default(true);
            $table->string('origin')->nullable(); // origine du produit
            $table->date('harvest_date')->nullable(); // date de récolte
            $table->date('expiry_date')->nullable(); // date de péremption
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['is_active', 'stock_quantity']);
            $table->index(['category_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
