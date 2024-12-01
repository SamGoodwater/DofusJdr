<?php

use App\Models\Classe;
use App\Models\Creature;
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
            $table->foreignIdFor(\App\Models\Creature::class)->nullable()->constrained()->cascadeOnDelete();
            $table->primary(['creature_id']);
            $table->string('story')->nullable();
            $table->string('historical')->nullable();
            $table->string('age')->nullable();
            $table->string('size')->nullable();

            $table->foreignIdFor(Classe::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Specialization::class)->nullable()->constrained()->cascadeOnDelete();
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
        Schema::table('npcs', function (Blueprint $table) {
            $table->dropForeignIdFor(Classe::class);
        });
        Schema::table('npcs', function (Blueprint $table) {
            $table->dropForeignIdFor(Creature::class);
        });
    }
};
