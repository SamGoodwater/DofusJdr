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

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('official_id')->nullable();
            $table->string('dofusdb_id')->nullable();
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('name');
            $table->string('description_fast')->nullable();
            $table->string('description')->nullable();
            $table->string('life')->nullable();
            $table->integer('life_dice')->default(10);
            $table->string('specificity')->nullable();
            $table->integer('weapons_of_choice')->nullable();
            $table->boolean('usable')->default(false);
            $table->string('dofus_version')->default('3');
            $table->softDeletes();
        });

        Schema::create('classe_spell', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Classe::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Spell::class)->constrained()->cascadeOnDelete();
            $table->primary(['classe_id', 'spell_id']);
            $table->softDeletes();
        });

        Schema::create('capability_classe', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Capability::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Classe::class)->constrained()->cascadeOnDelete();
            $table->primary(['capability_id', 'classe_id']);
            $table->softDeletes();
        });

        Schema::create('attribute_classe', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Classe::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Attribute::class)->constrained()->cascadeOnDelete();
            $table->primary(['classe_id', 'attribute_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
        Schema::dropIfExists('classe_spell');
        Schema::dropIfExists('capability_classe');
        Schema::dropIfExists('attribute_classe');
    }
};
