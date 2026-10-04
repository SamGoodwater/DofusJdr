<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creatures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('hostility')->default(2);
            $table->string('location')->nullable();
            $table->string('level')->default('1');
            $table->string('other_info')->nullable();
            $table->string('life')->default('30');
            $table->string('pa')->default('6');
            $table->string('pm')->default('3');
            $table->string('po')->default('0');
            $table->string('ini')->default('0');
            $table->string('invocation')->default('0');
            $table->string('touch')->default('0');
            $table->string('ca')->default('0');
            $table->string('dodge_pa')->default('0');
            $table->string('dodge_pm')->default('0');
            $table->string('fuite')->default('0');
            $table->string('tacle')->default('0');
            $table->string('critical_hit', 16)->default('0');
            $table->string('heal_bonus', 16)->default('0');
            $table->string('vitality')->default('0');
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
            $table->string('do_sagesse')->default('0');
            $table->string('do_vitalite')->default('0');
            // TEXT sans DEFAULT SQL (MySQL 8 / SQLSTATE 1101). Plus tard nullable : null = composition.
            $table->text('res_fixe_neutre');
            $table->text('res_fixe_terre');
            $table->text('res_fixe_feu');
            $table->text('res_fixe_air');
            $table->text('res_fixe_eau');
            $table->string('res_neutre')->default('0');
            $table->string('res_terre')->default('0');
            $table->string('res_feu')->default('0');
            $table->string('res_air')->default('0');
            $table->string('res_eau')->default('0');
            $table->string('res_sagesse')->default('0');
            $table->string('res_vitalite')->default('0');
            $table->string('acrobatie_bonus')->default('0');
            $table->string('discretion_bonus')->default('0');
            $table->string('escamotage_bonus')->default('0');
            $table->string('athletisme_bonus')->default('0');
            $table->string('intimidation_bonus')->default('0');
            $table->string('arcane_bonus')->default('0');
            $table->string('histoire_bonus')->default('0');
            $table->string('investigation_bonus')->default('0');
            $table->string('nature_bonus')->default('0');
            $table->string('religion_bonus')->default('0');
            $table->string('dressage_bonus')->default('0');
            $table->string('medecine_bonus')->default('0');
            $table->string('perception_bonus')->default('0');
            $table->string('perspicacite_bonus')->default('0');
            $table->string('survie_bonus')->default('0');
            $table->string('persuasion_bonus')->default('0');
            $table->string('representation_bonus')->default('0');
            $table->string('supercherie_bonus')->default('0');
            $table->tinyInteger('acrobatie_mastery')->default(0);
            $table->tinyInteger('discretion_mastery')->default(0);
            $table->tinyInteger('escamotage_mastery')->default(0);
            $table->tinyInteger('athletisme_mastery')->default(0);
            $table->tinyInteger('intimidation_mastery')->default(0);
            $table->tinyInteger('arcane_mastery')->default(0);
            $table->tinyInteger('histoire_mastery')->default(0);
            $table->tinyInteger('investigation_mastery')->default(0);
            $table->tinyInteger('nature_mastery')->default(0);
            $table->tinyInteger('religion_mastery')->default(0);
            $table->tinyInteger('dressage_mastery')->default(0);
            $table->tinyInteger('medecine_mastery')->default(0);
            $table->tinyInteger('perception_mastery')->default(0);
            $table->tinyInteger('perspicacite_mastery')->default(0);
            $table->tinyInteger('survie_mastery')->default(0);
            $table->tinyInteger('persuasion_mastery')->default(0);
            $table->tinyInteger('representation_mastery')->default(0);
            $table->tinyInteger('supercherie_mastery')->default(0);
            $table->unsignedTinyInteger('save_vitality_bonus')->default(0);
            $table->unsignedTinyInteger('save_wisdom_bonus')->default(0);
            $table->unsignedTinyInteger('save_strength_bonus')->default(0);
            $table->unsignedTinyInteger('save_intelligence_bonus')->default(0);
            $table->unsignedTinyInteger('save_chance_bonus')->default(0);
            $table->unsignedTinyInteger('save_agility_bonus')->default(0);
            $table->unsignedTinyInteger('save_vitality_mastery')->default(0);
            $table->unsignedTinyInteger('save_wisdom_mastery')->default(0);
            $table->unsignedTinyInteger('save_strength_mastery')->default(0);
            $table->unsignedTinyInteger('save_intelligence_mastery')->default(0);
            $table->unsignedTinyInteger('save_chance_mastery')->default(0);
            $table->unsignedTinyInteger('save_agility_mastery')->default(0);
            $table->string('kamas')->nullable();
            $table->string('drop_')->nullable();
            $table->string('other_item')->nullable();
            $table->string('other_consumable')->nullable();
            $table->string('other_resource')->nullable();
            $table->string('other_spell')->nullable();
            $table->string('state')->default('draft');
            $table->tinyInteger('read_level')->default(0);
            $table->tinyInteger('write_level')->default(3);
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creatures');
    }
};
