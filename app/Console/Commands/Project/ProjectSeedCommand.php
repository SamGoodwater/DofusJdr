<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\AcceptsYesNoFlags;
use App\Console\Concerns\GuardsProductionEnvironment;
use App\Console\YesNoFlags;
use Illuminate\Console\Command;

/**
 * Ensemencement « données locales » sans appels réseau DofusDB (types API ni scrapping entités).
 *
 * Délègue à {@see ProjectInitCommand} avec `--skip-scrapping` et `--skip-types` :
 * migrations, seeders Krosmoz, import règles, capacités legacy, sync pages bibliothèque.
 *
 * Par défaut : création seule (ne touche pas une ligne déjà en base).
 * `--overwrite` réécrit depuis les fichiers — confirmation obligatoire (CI : `-y`).
 *
 * @example php artisan project:seed
 * @example php artisan project:seed --fresh
 * @example php artisan project:seed --overwrite -y
 */
class ProjectSeedCommand extends Command
{
    use AcceptsYesNoFlags;
    use GuardsProductionEnvironment;

    protected $signature = 'project:seed
        {--fresh : migrate:fresh --force avant les seeders}
        {--skip-migrate : Ne pas lancer les migrations}
        {--overwrite : DANGER — réécrit les lignes déjà en base depuis les JSON/PHP (confirmation via project:init ; CI : -y)}
        {--skip-capabilities : Ne pas importer capabilities:import-legacy}
        {--skip-specializations : Ne pas exécuter SpecializationSeeder (HTML legacy + brouillons)}
        {--init-scheduler : Afficher la ligne cron scheduler (comme project:init)}
        {--skip-notify : Ne pas notifier les admin à la fin}
        {--skip-super-admin-prompt : Ne pas demander la création du super_admin (CI / scripts)}
        {--skip-downloads : Ne pas compiler le livre de règles (PDF / ODT)}
        '.YesNoFlags::SIGNATURE;

    protected $description = 'Seeders et données locales (sans types ni scrapping DofusDB) — délègue à project:init';

    public function handle(): int
    {
        if (! $this->guardNotProduction(
            'project:seed est interdit en production. Utilisez des migrations et seeders ciblés.'
        )) {
            return ArtisanExitCode::FAILURE;
        }

        if ($this->abortIfConflictingYesNoFlags()) {
            return ArtisanExitCode::FAILURE;
        }

        $this->info('=== project:seed (données locales, sans DofusDB) ===');
        $this->line('  → project:init --skip-scrapping --skip-types');
        if ((bool) $this->option('overwrite')) {
            $this->warn('  → --overwrite : écrasement des lignes existantes (confirmation demandée)');
            $this->line('  Avant d’écraser : php artisan scrapping:seeders:export --characteristics');
        }
        $this->newLine();

        return $this->call('project:init', $this->buildProjectInitArguments());
    }

    /**
     * @return array<string, bool>
     */
    private function buildProjectInitArguments(): array
    {
        $arguments = [
            '--skip-scrapping' => true,
            '--skip-types' => true,
        ];

        foreach ([
            'fresh',
            'skip-migrate',
            'overwrite',
            'skip-capabilities',
            'skip-specializations',
            'init-scheduler',
            'skip-notify',
            'skip-super-admin-prompt',
            'skip-downloads',
        ] as $name) {
            if ($this->option($name)) {
                $arguments['--'.$name] = true;
            }
        }

        return array_merge($arguments, $this->yesNoCallOptions());
    }
}
