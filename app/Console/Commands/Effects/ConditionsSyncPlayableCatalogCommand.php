<?php

declare(strict_types=1);

namespace App\Console\Commands\Effects;

use App\Console\ArtisanExitCode;
use App\Services\Condition\PlayableConditionCatalog;
use Illuminate\Console\Command;

/**
 * Ajoute les états D&D et Dofus du catalogue, sans réécrire les fiches déjà jouables.
 */
final class ConditionsSyncPlayableCatalogCommand extends Command
{
    protected $signature = 'conditions:sync-playable-catalog
                            {--delete-raw : Supprime les états encore en Brut après recollement des sorts}';

    protected $description = 'Pose les états jouables D&D et Dofus du catalogue ; les fiches déjà jouables sont laissées intactes';

    public function handle(PlayableConditionCatalog $catalog): int
    {
        $stats = $catalog->sync(deleteRaw: (bool) $this->option('delete-raw'));

        $this->info('Créés : '.$stats['created']);
        $this->info('Repris depuis un Brut : '.$stats['updated']);
        $this->info('Déjà jouables, non modifiés : '.$stats['skipped']);
        if ($this->option('delete-raw')) {
            $this->info('Bruts supprimés : '.$stats['deleted']);
        }

        return ArtisanExitCode::SUCCESS;
    }
}
