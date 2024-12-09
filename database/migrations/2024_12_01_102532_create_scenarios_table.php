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
            $table->boolean('is_visible')->default(false);
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignIdFor(\App\Models\User::class, 'created_by')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('file_sscenario', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Modules\Scenario::class)->constrained()->cascadeOnDelete();
            $table->string('file');
            $table->softDeletes();
        });

        Schema::create('scenario_page', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Scenario::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Page::class)->constrained()->onDelete('cascade');
            $table->integer('order_num')->default(0);
            $table->boolean('visible')->default(true);
            $table->primary(['scenario_id', 'page_id']);
            $table->softDeletes();
        });

        Schema::create('consumable_scenario', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Consumable::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Modules\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['consumable_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('item_scenario', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Item::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Modules\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['item_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('npc_scenario', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Npc::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Modules\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['npc_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('scenario_user', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Modules\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['user_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('mob_scenario', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Mob::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Modules\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['mob_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('scenario_shop', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Shop::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Modules\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['shop_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('scenario_spell', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Spell::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Modules\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['spell_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('campaign_scenario', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Campaign::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Modules\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['campaign_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('ressource_scenario', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Ressource::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Modules\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['ressource_id', 'scenario_id']);
            $table->softDeletes();
        });

        Schema::create('scenario_panoply', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Panoply::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Modules\Scenario::class)->constrained()->onDelete('cascade');
            $table->primary(['panoply_id', 'scenario_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenarios');
        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'created_by');
        });
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
        Schema::dropIfExists('scenario_panoply');
    }
};
