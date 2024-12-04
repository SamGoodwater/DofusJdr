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
        Schema::create('conditions', function (Blueprint $table) {
            $table->id();
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_unbewitchable')->default(false);
            $table->boolean('is_malus')->default(true);
            $table->boolean('usable')->default(false);
            $table->softDeletes();

            $table->foreignIdFor(\App\Models\User::class, 'created_by')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conditions');
        Schema::table('conditions', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'created_by');
        });
    }
};
