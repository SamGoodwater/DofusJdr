<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Formule de génération des grilles de normes (5 paliers × 20 niveaux).
 *
 * Quand norms_formula est renseignée, norms_grid est le résultat calculé
 * par NormsGridGenerator (variables [level], [power], [clé]).
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['characteristic_creature', 'characteristic_object', 'characteristic_spell'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->text('norms_formula')->nullable()->after('norms_grid');
            });
        }
    }

    public function down(): void
    {
        foreach (['characteristic_creature', 'characteristic_object', 'characteristic_spell'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropColumn('norms_formula');
            });
        }
    }
};
