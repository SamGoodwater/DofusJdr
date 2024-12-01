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
        Schema::create('scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('uniqid', 20)->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('slug')->unique();
            $table->string('keyword')->nullable();
            $table->boolean('is_public')->default(false);
            $table->tinyInteger('state')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('scenario_page', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Scenario::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Page::class)->constrained()->onDelete('cascade');
            $table->integer('order_num')->default(0);
            $table->boolean('visible')->default(true);
            $table->primary(['scenario_id', 'page_id']);
            $table->softDeletes();
        });

        Schema::create('consumable_scenario', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Consumable::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['consumable_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('item_scenario', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Item::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['item_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('npc_scenario', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Npc::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['npc_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('scenario_user', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['user_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('mob_scenario', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Mob::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['mob_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('scenario_shop', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Shop::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['shop_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('scenario_spell', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Spell::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['spell_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('campaign_scenario', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Campaign::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['campaign_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('ressource_scenario', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Ressource::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['ressource_id', 'scenario_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenarios');
        Schema::dropIfExists('scenario_page');
        Schema::dropIfExists('consumable_scenario');
        Schema::dropIfExists('item_scenario');
        Schema::dropIfExists('npc_scenario');
        Schema::dropIfExists('scenario_user');
        Schema::dropIfExists('mob_scenario');
        Schema::dropIfExists('scenario_shop');
        Schema::dropIfExists('scenario_spell');
        Schema::dropIfExists('campaign_scenario');
        Schema::dropIfExists('ressource_scenario');
    }
};
