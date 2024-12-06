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
        Schema::create('panoplies', function (Blueprint $table) {
            $table->id();
            $table->string('uniqid', 24)->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('bonus')->nullable();
            $table->boolean('usable')->default(true);
            $table->boolean('is_visible')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreignIdFor(\App\Models\User::class, 'created_by')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panoplies');
        Schema::table('panoplies', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'created_by');
        });
    }
};
