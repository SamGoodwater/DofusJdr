<?php

use App\Models\Page;
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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('name');
            $table->string('keyword')->nullable();
            $table->string('slug');
            $table->integer('order_num')->default(0);
            $table->boolean('is_dropdown')->default(false);
            $table->boolean('public')->default(true);
            $table->boolean('is_editable')->default(true);

            $table->foreignIdFor(Page::class)->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('component')->default('0');
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->integer('order_num')->default(0);
            $table->boolean('visible')->default(true);
            $table->softDeletes();

            $table->foreignIdFor(Page::class)->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
        Schema::dropIfExists('sections');
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeignIdFor(Page::class);
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeignIdFor(Page::class);
        });
    }
};
