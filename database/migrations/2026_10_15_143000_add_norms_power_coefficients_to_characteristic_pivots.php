<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Coefficients de palier pour générer norms_grid depuis une formule « neutre ».
 *
 * Format JSON : {"very_weak":0.5,"weak":0.75,"strong":1.3,"very_strong":1.7}
 * (le palier neutral est toujours 1). Quand renseigné, NormsGridGenerator
 * évalue la formule une fois par niveau au palier neutre puis multiplie.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'characteristic_creature',
            'characteristic_object',
            'characteristic_spell',
            'characteristic_consumable',
            'characteristic_profile',
        ] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->json('norms_power_coefficients')->nullable()->after('norms_formula');
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'characteristic_creature',
            'characteristic_object',
            'characteristic_spell',
            'characteristic_consumable',
            'characteristic_profile',
        ] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropColumn('norms_power_coefficients');
            });
        }
    }
};
