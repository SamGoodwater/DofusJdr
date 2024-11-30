<?php

use App\Models\Specialization;
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

        Schema::create('npcs', function (Blueprint $table) {
            $table->id();
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('name');
            $table->string('classe');
            $table->string('description')->nullable();
            $table->string('location')->nullable();
            $table->string('story')->nullable();
            $table->string('historical')->nullable();
            $table->integer('level')->default(1);
            $table->string('trait')->nullable();
            $table->string('other_info')->nullable();
            $table->string('age')->nullable();
            $table->string('size')->nullable();
            $table->integer('life')->default(30);
            $table->integer('pa')->default(6);
            $table->integer('pm')->default(3);
            $table->integer('po')->default(0);
            $table->integer('ini')->default(0);
            $table->integer('invocation')->default(0);
            $table->integer('touch')->default(0);
            $table->integer('ca')->default(0);
            $table->integer('dodge_pa')->default(0);
            $table->integer('dodge_pm')->default(0);
            $table->integer('fuite')->default(0);
            $table->integer('tacle')->default(0);
            $table->integer('vitality')->default(0);
            $table->integer('sagesse')->default(0);
            $table->integer('strong')->default(0);
            $table->integer('intel')->default(0);
            $table->integer('agi')->default(0);
            $table->integer('chance')->default(0);
            $table->string('do_fixe_neutre')->default('0');
            $table->string('do_fixe_terre')->default('0');
            $table->string('do_fixe_feu')->default('0');
            $table->string('do_fixe_air')->default('0');
            $table->string('do_fixe_eau')->default('0');
            $table->integer('res_neutre')->default(0);
            $table->integer('res_terre')->default(0);
            $table->integer('res_feu')->default(0);
            $table->integer('res_air')->default(0);
            $table->integer('res_eau')->default(0);
            $table->integer('acrobatie_bonus')->default(0);
            $table->integer('discretion_bonus')->default(0);
            $table->integer('escamotage_bonus')->default(0);
            $table->integer('athletisme_bonus')->default(0);
            $table->integer('intimidation_bonus')->default(0);
            $table->integer('arcane_bonus')->default(0);
            $table->integer('histoire_bonus')->default(0);
            $table->integer('investigation_bonus')->default(0);
            $table->integer('nature_bonus')->default(0);
            $table->integer('religion_bonus')->default(0);
            $table->integer('dressage_bonus')->default(0);
            $table->integer('medecine_bonus')->default(0);
            $table->integer('perception_bonus')->default(0);
            $table->integer('perspicacite_bonus')->default(0);
            $table->integer('survie_bonus')->default(0);
            $table->integer('persuasion_bonus')->default(0);
            $table->integer('representation_bonus')->default(0);
            $table->integer('supercherie_bonus')->default(0);
            $table->integer('acrobatie_mastery')->default(0);
            $table->integer('discretion_mastery')->default(0);
            $table->integer('escamotage_mastery')->default(0);
            $table->integer('athletisme_mastery')->default(0);
            $table->integer('intimidation_mastery')->default(0);
            $table->integer('arcane_mastery')->default(0);
            $table->integer('histoire_mastery')->default(0);
            $table->integer('investigation_mastery')->default(0);
            $table->integer('nature_mastery')->default(0);
            $table->integer('religion_mastery')->default(0);
            $table->integer('dressage_mastery')->default(0);
            $table->integer('medecine_mastery')->default(0);
            $table->integer('perception_mastery')->default(0);
            $table->integer('perspicacite_mastery')->default(0);
            $table->integer('survie_mastery')->default(0);
            $table->integer('persuasion_mastery')->default(0);
            $table->integer('representation_mastery')->default(0);
            $table->integer('supercherie_mastery')->default(0);
            $table->string('kamas')->nullable();
            $table->string('drop_')->nullable();
            $table->string('other_item')->nullable();
            $table->string('other_consumable')->nullable();
            $table->string('other_ressource')->nullable();
            $table->string('other_spell')->nullable();
            $table->boolean('usable')->default(true);

            $table->foreignIdFor(Specialization::class)->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('capability_npc', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\npc::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Capability::class)->constrained()->cascadeOnDelete();
            $table->primary(['npc_id', 'capability_id']);
        });

        Schema::create('consumable_npc', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\npc::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Consumable::class)->constrained()->cascadeOnDelete();
            $table->string('quantity')->default(1);
            $table->primary(['npc_id', 'consumable_id']);
        });

        Schema::create('item_npc', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\npc::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Item::class)->constrained()->cascadeOnDelete();
            $table->string('quantity')->default(1);
            $table->primary(['npc_id', 'item_id']);
        });

        Schema::create('npc_spell', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\npc::class);
            $table->foreignIdFor(\App\Models\Spell::class);
            $table->primary(['npc_id', 'spell_id']);
        });

        Schema::create('npc_ressource', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\npc::class);
            $table->foreignIdFor(\App\Models\Ressource::class);
            $table->string('quantity')->default('1');
            $table->primary(['npc_id', 'ressource_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('npcs');
        Schema::table('npcs', function (Blueprint $table) {
            $table->dropForeignIdFor(Specialization::class);
        });
        Schema::dropIfExists('capability_npc');
        Schema::dropIfExists('consumable_npc');
        Schema::dropIfExists('item_npc');
        Schema::dropIfExists('npc_spell');
        Schema::dropIfExists('npc_ressource');
    }
};
