<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('breeds', function (Blueprint $table) {
            $table->longText('mechanics')->nullable()->after('specificity');
        });
    }

    public function down(): void
    {
        Schema::table('breeds', function (Blueprint $table) {
            $table->dropColumn('mechanics');
        });
    }
};
