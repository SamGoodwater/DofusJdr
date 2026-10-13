<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Groupe Consommable : buff/débuff temporaires, valeurs d'action, descriptifs et durée.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('characteristic_consumable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('characteristic_id')->constrained('characteristics')->cascadeOnDelete();
            $table->unsignedInteger('dofusdb_characteristic_id')->nullable();
            $table->string('entity', 32)->default('*')->comment('consumable ou *');
            $table->string('db_column', 64)->nullable();
            $table->string('min', 512)->nullable();
            $table->string('max', 512)->nullable();
            $table->text('formula')->nullable();
            $table->text('formula_display')->nullable();
            $table->string('default_value', 512)->nullable();
            $table->text('conversion_formula')->nullable();
            $table->string('conversion_function', 64)->nullable();
            $table->json('conversion_dofus_sample')->nullable();
            $table->json('conversion_krosmoz_sample')->nullable();
            $table->json('conversion_sample_rows')->nullable();
            $table->json('norms_grid')->nullable();
            $table->text('norms_formula')->nullable();
            $table->json('norms_conditions')->nullable();
            $table->text('norms_description')->nullable();
            $table->foreignId('norms_help_section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->json('value_available')->nullable();
            $table->timestamps();

            $table->unique(['characteristic_id', 'entity'], 'char_consumable_char_entity_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characteristic_consumable');
    }
};
