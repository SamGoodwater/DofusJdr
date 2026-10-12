<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\Characteristic\Norms\NormsGridGenerator;
use Illuminate\Database\Seeder;

/**
 * Régénère norms_grid depuis norms_formula après le seed des pivots.
 */
final class RegenerateCharacteristicNormsSeeder extends Seeder
{
    public function run(NormsGridGenerator $generator): void
    {
        $written = $generator->regenerateAll();
        if ($this->command) {
            $this->command->info("NormsGridGenerator : {$written} grille(s) régénérée(s).");
        }
    }
}
