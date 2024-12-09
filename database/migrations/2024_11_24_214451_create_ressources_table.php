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
        Schema::create('ressources', function (Blueprint $table) {
            $table->id();
            $table->string('dofusdb_id')->nullable();
            $table->integer('official_id')->nullable();
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('level')->default(1);
            $table->string('price')->nullable();
            $table->string('weight')->nullable();
            $table->integer('rarity')->default(5);
            $table->boolean('usable')->default(true);
            $table->string('dofus_version')->default('3');
            $table->boolean('is_visible')->default(false);
            $table->string('image')->nullable();
            $table->boolean('auto_update')->default(true);
            $table->softDeletes();

            $table->foreignIdFor(\App\Models\Modules\Ressourcetype::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\User::class, 'created_by')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ressources');
        Schema::table('ressources', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'created_by');
        });
        Schema::table('ressources', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Modules\Ressourcetype::class);
        });
    }
};
