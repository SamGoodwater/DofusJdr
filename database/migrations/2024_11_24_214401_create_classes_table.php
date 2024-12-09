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
            $table->boolean('usable')->default(false);
            $table->string('dofus_version')->default('3');
            $table->boolean('is_visible')->default(false);
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('auto_update')->default(true);
            $table->softDeletes();

            $table->foreignIdFor(\App\Models\User::class, 'created_by')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('classe_spell', function (Blueprint $table) {
            $table->foreignIdFor(App\Models\Modules\Classe::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Modules\Spell::class)->constrained()->cascadeOnDelete();
            $table->primary(['classe_id', 'spell_id']);
            $table->softDeletes();
        });

        Schema::create('capability_classe', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Capability::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(App\Models\Modules\Classe::class)->constrained()->cascadeOnDelete();
            $table->primary(['capability_id', 'classe_id']);
            $table->softDeletes();
        });

        Schema::create('attribute_classe', function (Blueprint $table) {
            $table->foreignIdFor(App\Models\Modules\Classe::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Modules\Attribute::class)->constrained()->cascadeOnDelete();
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
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'created_by');
        });
    }
};
