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
        Schema::create('campaigns', function (Blueprint $table) {
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

        Schema::create('campaign_page', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Campaign::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Page::class)->constrained()->onDelete('cascade');
            $table->integer('order_num')->default(0);
            $table->boolean('visible')->default(true);
            $table->primary(['campaign_id', 'page_id']);
            $table->softDeletes();
        });

        Schema::create('consumable_campaign', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Consumable::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Campaign::class)->constrained()->onDelete('cascade');
            $table->primary(['consumable_id', 'campaign_id']);
            $table->softDeletes();
        });

        Schema::create('item_campaign', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Item::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Campaign::class)->constrained()->onDelete('cascade');
            $table->primary(['item_id', 'campaign_id']);
            $table->softDeletes();
        });

        Schema::create('npc_campaign', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Npc::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Campaign::class)->constrained()->onDelete('cascade');
            $table->primary(['npc_id', 'campaign_id']);
            $table->softDeletes();
        });

        Schema::create('campaign_user', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Campaign::class)->constrained()->onDelete('cascade');
            $table->primary(['user_id', 'campaign_id']);
            $table->softDeletes();
        });

        Schema::create('mob_campaign', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Mob::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Campaign::class)->constrained()->onDelete('cascade');
            $table->primary(['mob_id', 'campaign_id']);
            $table->softDeletes();
        });

        Schema::create('campaign_shop', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Shop::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Campaign::class)->constrained()->onDelete('cascade');
            $table->primary(['shop_id', 'campaign_id']);
            $table->softDeletes();
        });

        Schema::create('campaign_spell', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Spell::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Campaign::class)->constrained()->onDelete('cascade');
            $table->primary(['spell_id', 'campaign_id']);
            $table->softDeletes();
        });

        Schema::create('ressource_campaign', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Ressource::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Campaign::class)->constrained()->onDelete('cascade');
            $table->primary(['ressource_id', 'campaign_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('campaign_page');
        Schema::dropIfExists('consumable_campaign');
        Schema::dropIfExists('item_campaign');
        Schema::dropIfExists('npc_campaign');
        Schema::dropIfExists('campaign_user');
        Schema::dropIfExists('mob_campaign');
        Schema::dropIfExists('campaign_shop');
        Schema::dropIfExists('campaign_spell');
        Schema::dropIfExists('ressource_campaign');
    }
};
