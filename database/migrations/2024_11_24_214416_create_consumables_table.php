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
        Schema::create('consumables', function (Blueprint $table) {
            $table->id();
            $table->string('official_id')->nullable();
            $table->string('dofusdb_id')->nullable();
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('effect')->nullable();
            $table->integer('level')->nullable();
            $table->string('recepe')->nullable();
            $table->string('price')->nullable();
            $table->integer('rarity')->default(5);
            $table->boolean('usable')->default(false);
            $table->string('dofus_version')->default('3');
            $table->boolean('is_visible')->default(false);
            $table->string('image')->nullable();
            $table->boolean('auto_update')->default(true);
            $table->softDeletes();

            $table->foreignIdFor(\App\Models\Modules\Consumabletype::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\User::class, 'created_by')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('consumable_ressource', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Consumable::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Modules\Ressource::class)->constrained()->cascadeOnDelete();
            $table->string('quantity')->default('1');
            $table->primary(['consumable_id', 'ressource_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumables');
        Schema::dropIfExists('consumable_ressource');
        Schema::table('consumables', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'created_by');
        });
        Schema::table('consumables', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Modules\Consumabletype::class);
        });
    }
};
