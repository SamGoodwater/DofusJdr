<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Limite absolue : min/max appliqués au total permanent au runtime (hors buffs éphémères).
 * Null sur une surcharge d'entité = hérite de la ligne `*`.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('characteristic_creature') || Schema::hasColumn('characteristic_creature', 'limit_absolute')) {
            return;
        }

        Schema::table('characteristic_creature', function (Blueprint $table): void {
            $table->boolean('limit_absolute')->nullable()->after('max');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('characteristic_creature', 'limit_absolute')) {
            Schema::table('characteristic_creature', function (Blueprint $table): void {
                $table->dropColumn('limit_absolute');
            });
        }
    }
};
