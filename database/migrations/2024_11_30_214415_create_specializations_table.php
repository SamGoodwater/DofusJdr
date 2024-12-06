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
            $table->boolean('is_visible')->default(false);
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignIdFor(\App\Models\Page::class)->nullable()->constrained()->cascadeOnDelete();

            $table->foreignIdFor(\App\Models\User::class, 'created_by')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('capabilitys_specializations', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Capability::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Specialization::class)->constrained()->cascadeOnDelete();
            $table->primary(['capability_id', 'specialization_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specializations');
        Schema::table('specializations', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'created_by');
        });
        Schema::table('specializations', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Page::class);
        });
        Schema::dropIfExists('capabilitys_specializations');
    }
};
