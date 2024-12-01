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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('official_id')->nullable();
            $table->string('dofusdb_id')->nullable();
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('name');
            $table->integer('level')->default(1);
            $table->string('description')->nullable();
            $table->integer('type')->default(1);
            $table->string('effect');
            $table->string('bonus')->nullable();
            $table->string('recepe')->nullable();
            $table->string('actif')->default('0');
            $table->string('twohands')->default('0');
            $table->string('pa')->default('1');
            $table->string('po')->default('1');
            $table->string('price')->nullable();
            $table->integer('rarity')->default(5);
            $table->boolean('usable')->default(false);
            $table->string('dofus_version')->default('3');
            $table->softDeletes();
        });

        Schema::create('item_ressource', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Item::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Ressource::class)->constrained()->cascadeOnDelete();
            $table->string('quantity')->default('1');
            $table->primary(['item_id', 'ressource_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
        Schema::dropIfExists('item_ressource');
    }
};
