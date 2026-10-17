<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conditions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('dofusdb_id')->nullable()->unique();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('state')->default('draft');
            $table->tinyInteger('read_level')->default(0);
            $table->tinyInteger('write_level')->default(3);
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->boolean('prevents_spell_cast')->default(false);
            $table->boolean('prevents_fight')->default(false);
            $table->boolean('cant_be_moved')->default(false);
            $table->boolean('cant_be_pushed')->default(false);
            $table->boolean('cant_deal_damage')->default(false);
            $table->boolean('invulnerable')->default(false);
            $table->boolean('cant_switch_position')->default(false);
            $table->boolean('incurable')->default(false);
            $table->boolean('invulnerable_melee')->default(false);
            $table->boolean('invulnerable_range')->default(false);
            $table->boolean('cant_tackle')->default(false);
            $table->boolean('cant_be_tackled')->default(false);
            $table->boolean('display_turn_remaining')->default(false);
            $table->boolean('is_main_state')->default(false);
            $table->boolean('dissipable')->default(true);
            $table->json('raw')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conditions');
    }
};
