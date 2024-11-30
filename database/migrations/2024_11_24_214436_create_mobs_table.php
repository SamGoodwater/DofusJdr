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
        Schema::create('mobs', function (Blueprint $table) {
            $table->id();
            $table->string('official_id')->nullable();
            $table->string('dofusdb_id')->nullable();
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('level')->default('1');
            $table->string('life')->default('0');
            $table->string('vitality')->default('10');
            $table->string('pa')->default('1');
            $table->string('pm')->default('1');
            $table->string('po')->default('0');
            $table->string('ini')->default('0');
            $table->string('touch')->nullable();
            $table->string('sagesse')->default('0');
            $table->string('strong')->default('0');
            $table->string('intel')->default('0');
            $table->string('agi')->default('0');
            $table->string('chance')->default('0');
            $table->string('do_fixe_neutre')->default('0');
            $table->string('do_fixe_terre')->default('0');
            $table->string('do_fixe_feu')->default('0');
            $table->string('do_fixe_air')->default('0');
            $table->string('do_fixe_eau')->default('0');
            $table->string('do_fixe_multiple')->default('0');
            $table->string('ca')->default('0');
            $table->string('fuite')->default('0');
            $table->string('tacle')->default('0');
            $table->string('dodge_pa')->default('0');
            $table->string('dodge_pm')->default('0');
            $table->string('res_neutre')->default('0');
            $table->string('res_terre')->default('0');
            $table->string('res_feu')->default('0');
            $table->string('res_air')->default('0');
            $table->string('res_eau')->default('0');
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
            $table->string('location')->nullable();
            $table->integer('hostility')->default(2);
            $table->integer('size')->default(2);
            $table->string('trait')->nullable();
            $table->string('kamas')->nullable();
            $table->string('drop_')->nullable();
            $table->string('other_info')->nullable();
            $table->string('other_item')->nullable();
            $table->string('other_consumable')->nullable();
            $table->string('other_spell')->nullable();
            $table->boolean('usable')->default(false);
            $table->string('dofus_version')->default('3');
        });

        Schema::create('capability_mob', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Mob::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Capability::class)->constrained()->cascadeOnDelete();
            $table->primary(['mob_id', 'capability_id']);
        });

        Schema::create('consumable_mob', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Mob::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Consumable::class)->constrained()->cascadeOnDelete();
            $table->string('quantity')->default(1);
            $table->primary(['mob_id', 'consumable_id']);
        });

        Schema::create('item_mob', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Mob::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Item::class)->constrained()->cascadeOnDelete();
            $table->string('quantity')->default(1);
            $table->primary(['mob_id', 'item_id']);
        });

        Schema::create('mob_spell', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Mob::class);
            $table->foreignIdFor(\App\Models\Spell::class);
            $table->primary(['mob_id', 'spell_id']);
        });

        Schema::create('mob_ressource', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Mob::class);
            $table->foreignIdFor(\App\Models\Ressource::class);
            $table->string('quantity')->default('1');
            $table->primary(['mob_id', 'ressource_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobs');
        Schema::dropIfExists('capabilitie_mob');
        Schema::dropIfExists('consumable_mob');
        Schema::dropIfExists('item_mob');
        Schema::dropIfExists('mob_spell');
        Schema::dropIfExists('mob_ressource');
    }
};
