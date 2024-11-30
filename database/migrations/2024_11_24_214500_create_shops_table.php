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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('location')->nullable();
            $table->integer('price')->default(0);
            $table->boolean('usable')->default(true);

            $table->foreignIdFor(\App\Models\Npc::class)->nullable();
        });

        Schema::create('consumable_shop', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Consumable::class);
            $table->foreignIdFor(\App\Models\Shop::class);
            $table->primary(['consumable_id', 'shop_id']);
            $table->string('quantity')->nullable();
            $table->string('price')->nullable();
            $table->string('comment')->nullable();
        });

        Schema::create('item_shop', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Item::class);
            $table->foreignIdFor(\App\Models\Shop::class);
            $table->primary(['item_id', 'shop_id']);
            $table->string('quantity')->nullable();
            $table->string('price')->nullable();
            $table->string('comment')->nullable();
        });

        Schema::create('ressource_shop', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Ressource::class);
            $table->foreignIdFor(\App\Models\Shop::class);
            $table->primary(['ressource_id', 'shop_id']);
            $table->string('quantity')->nullable();
            $table->string('price')->nullable();
            $table->string('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
        Schema::table('shops', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Npc::class);
        });
        Schema::dropIfExists('consumable_shop');
        Schema::dropIfExists('item_shop');
        Schema::dropIfExists('ressource_shop');
    }
};
