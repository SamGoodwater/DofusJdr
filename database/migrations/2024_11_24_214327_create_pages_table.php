<?php

use App\Models\Page;
use App\Models\User;
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
            $table->boolean('is_public')->default(false);
            $table->boolean('is_visible')->default(false);
            $table->boolean('is_editable')->default(true);
            $table->softDeletes();

            $table->foreignIdFor(Page::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('component')->default('0');
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->integer('order_num')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->softDeletes();

            $table->foreignIdFor(Page::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('file_section', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Section::class)->constrained()->cascadeOnDelete();
            $table->string('file');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('file_section');
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeignIdFor(Page::class);
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeignIdFor(Page::class);
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeignIdFor(User::class, 'created_by');
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeignIdFor(User::class, 'created_by');
        });
    }
};
