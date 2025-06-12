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
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'image')) {
                $table->string('image')->nullable()->after('description');
            }

            // Ajouter les colonnes pour l'IA
            $table->boolean('ai_generated')->default(false)->after('image');
            $table->text('ai_prompt')->nullable()->after('ai_generated');
            $table->timestamp('image_generated_at')->nullable()->after('ai_prompt');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['image', 'ai_generated', 'ai_prompt', 'image_generated_at']);
        });
    }
};
