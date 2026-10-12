<?php

declare(strict_types=1);

namespace App\Console\Concerns;

use App\Console\ArtisanExitCode;
use App\Support\Seeder\SeedMode;

/**
 * Confirmation avant d’activer le mode seed overwrite (fichiers → base).
 *
 * Sans confirmation (ou refus), aucune ligne déjà présente n’est réécrite.
 * En CI / non interactif : passer `-y` avec `--overwrite`.
 *
 * @example
 * if ($this->confirmSeedOverwriteOrAbort() !== null) {
 *     return ArtisanExitCode::FAILURE;
 * }
 */
trait ConfirmsSeedOverwrite
{
    /**
     * Demande confirmation si `--overwrite` ou `SEED_OVERWRITE=true`.
     *
     * @return int|null Code d’échec Artisan, ou null pour continuer
     */
    protected function confirmSeedOverwriteOrAbort(): ?int
    {
        $viaOption = $this->hasOption('overwrite') && (bool) $this->option('overwrite');
        $viaEnv = (bool) config('seeders.overwrite', false);

        if (! $viaOption && ! $viaEnv) {
            return null;
        }

        $this->newLine();
        $this->warn('⚠  Mode OVERWRITE demandé : les seeders vont RÉÉCRIRE les lignes déjà en base');
        $this->warn('   (caractéristiques, entités seedées, etc.) à partir des fichiers JSON/PHP.');
        $this->line('   La base est normalement la source de vérité.');
        $this->line('   Avant d’écraser, exporte d’abord :');
        $this->line('   <fg=cyan>php artisan scrapping:seeders:export --characteristics</>');
        $this->line('   (ou <fg=cyan>project:backup</> hors production, qui met aussi à jour les seeds).');
        if ($viaEnv && ! $viaOption) {
            $this->line('   Déclencheur : variable d’environnement <fg=yellow>SEED_OVERWRITE=true</>.');
        }
        $this->newLine();

        $accepted = $this->confirmOrFlag(
            'Confirmer l’écrasement des lignes seedées déjà présentes en base ?',
            false
        );

        if (! $accepted) {
            SeedMode::forceOverwrite(false);
            if ($viaOption) {
                $this->info('Annulé : --overwrite refusé. Aucune écriture destructive.');

                return ArtisanExitCode::FAILURE;
            }
            $this->info('Overwrite env désactivé pour cette exécution (création seule).');

            return null;
        }

        SeedMode::forceOverwrite(true);
        $this->warn('Mode seed : OVERWRITE activé (fichiers → base sur lignes existantes).');

        return null;
    }
}
