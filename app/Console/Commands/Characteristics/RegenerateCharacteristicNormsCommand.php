<?php

declare(strict_types=1);

namespace App\Console\Commands\Characteristics;

use App\Console\ArtisanExitCode;
use App\Services\Characteristic\Norms\NormsGridGenerator;
use Illuminate\Console\Command;

/**
 * Régénère norms_grid depuis norms_formula.
 *
 * @example php artisan characteristics:regenerate-norms
 * @example php artisan characteristics:regenerate-norms --key=dommages_spell
 */
final class RegenerateCharacteristicNormsCommand extends Command
{
    protected $signature = 'characteristics:regenerate-norms
        {--key= : Limite à une clé de caractéristique}
        {--entity= : Limite à une entité pivot (*|spell|monster|…)}';

    protected $description = 'Régénère norms_grid à partir de norms_formula (ordre topologique)';

    public function handle(NormsGridGenerator $generator): int
    {
        $key = $this->option('key');
        $entity = $this->option('entity');
        $key = is_string($key) && $key !== '' ? $key : null;
        $entity = is_string($entity) && $entity !== '' ? $entity : null;

        if ($key !== null) {
            $written = $generator->regenerateKey($key, $entity);
            $this->info("{$written} grille(s) régénérée(s) pour {$key}"
                .($entity !== null ? " / {$entity}" : '').'.');
        } else {
            $written = $generator->regenerateAll();
            $this->info("{$written} grille(s) régénérée(s).");
        }

        return ArtisanExitCode::SUCCESS;
    }
}
