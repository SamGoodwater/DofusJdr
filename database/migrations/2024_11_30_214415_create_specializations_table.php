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
        Schema::create('specializations', function (Blueprint $table) {
            $table->id();
            $table->string('uniqid', 20)->unique();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('capabilitys_specializations', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Capability::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Specialization::class)->constrained()->cascadeOnDelete();
            $table->primary(['capability_id', 'specialization_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specializations');
        Schema::dropIfExists('capabilitys_specializations');
    }
};
