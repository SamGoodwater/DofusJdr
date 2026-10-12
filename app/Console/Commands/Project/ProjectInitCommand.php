<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\AcceptsYesNoFlags;
use App\Console\Concerns\ConfirmsSeedOverwrite;
use App\Console\Concerns\GuardsProductionEnvironment;
use App\Console\Concerns\NormalizesProjectSyncEntities;
use App\Console\Concerns\PromptsPrimarySuperAdmin;
use App\Console\Concerns\RunsBibliothequeEntityPagesSync;
use App\Console\YesNoFlags;
use App\Services\NotificationService;
use App\Support\Seeder\SeedMode;
use Database\Seeders\CreationPagesSeeder;
use Database\Seeders\CriticalPagesSeeder;
use Database\Seeders\Entity\BreedSeeder;
use Database\Seeders\Entity\CapabilitySeeder;
use Database\Seeders\Entity\ClassBreedSeeder;
use Database\Seeders\Entity\ConditionSeeder;
use Database\Seeders\Entity\ConsumableSeeder;
use Database\Seeders\Entity\CreatureTraitSeeder;
use Database\Seeders\Entity\ItemSeeder;
use Database\Seeders\Entity\LanguageSeeder;
use Database\Seeders\Entity\MonsterSeeder;
use Database\Seeders\Entity\NpcSeeder;
use Database\Seeders\Entity\PanoplySeeder;
use Database\Seeders\Entity\SpecializationSeeder;
use Database\Seeders\Entity\SpellSeeder;
use Database\Seeders\NavMenuSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\SectionSeeder;
use Database\Seeders\SubEffectSeeder;
use Database\Seeders\Type\SpellTypeSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Initialisation complète du projet : migrations, seeders, import règles, capacités (fichier local),
 * puis types et scrapping DofusDB (appels réseau en fin de pipeline).
 *
 * Phase seeders : `scrapping:setup` (types + caractéristiques + mappings), comptes/pages,
 * {@see SubEffectSeeder}, référentiels langues / conditions / traits, pages « Création »,
 * import legacy spécialisations (fichiers HTML optionnels), puis import TOC règles.
 * Capacités : commande `capabilities:import-legacy` sur `database/seeders/data/capability.json`.
 *
 * Les appels réseau vers DofusDB (`scrapping:types:seed`, `scrapping:races:seed`, `scrapping:run`)
 * sont exécutés en fin de pipeline : tu peux interrompre l’init après seeders / capacités
 * et garder une base utilisable pour les tests (`--skip-types`, `--skip-scrapping`).
 *
 * Transforme une base vide en un projet fonctionnel. Compatible exécution longue
 * (`set_time_limit(0)`, `DB::reconnect` entre phases). Notifie les admin/super_admin à la fin.
 *
 * @example php artisan project:init
 * @example php artisan project:init --fresh --noimage
 * @example php artisan project:init --skip-scrapping --entity=monster
 */
class ProjectInitCommand extends Command
{
    use AcceptsYesNoFlags;
    use ConfirmsSeedOverwrite;
    use GuardsProductionEnvironment;
    use NormalizesProjectSyncEntities;
    use PromptsPrimarySuperAdmin;
    use RunsBibliothequeEntityPagesSync;

    protected $signature = 'project:init
        {--deps : Exécuter d’abord project:deps (composer update + pnpm up + optimize)}
        {--fresh : migrate:fresh --force avant tout}
        {--skip-migrate : Ne pas lancer les migrations}
        {--skip-seeders : Ne pas exécuter les seeders (socle déjà fait)}
        {--overwrite : DANGER — réécrit les lignes déjà en base depuis les JSON/PHP (demande confirmation ; CI : -y)}
        {--skip-scrapping : Ne pas scraper}
        {--skip-capabilities : Ne pas importer les capabilities}
        {--skip-specializations : Ne pas exécuter le seeder des spécialisations (HTML legacy + brouillons)}
        {--skip-breeds : Ne pas reconstruire les sections CMS des classes (après scrapping)}
        {--skip-types : Ne pas extraire/seed les types (resources, consommables, équipements, races monstres)}
        {--noimage : Désactiver le téléchargement des images}
        {--skip-cache : Ignorer le cache HTTP pour le scrapping}
        {--entity= : Entités (virgules) : breed|class, spell, monster, resource, consumable, item, panoply}
        {--max-items=0 : Limite par entité (0=illimité)}
        {--update-mode=ignore : Mode remplacement existants: ignore|draft_raw_auto_update|auto_update|force (ignore=ne rien remplacer, reprise rapide)}
        {--simulate : Ne pas écrire en base (validation seule)}
        {--init-scheduler : Afficher la ligne cron pour le scheduler Laravel}
        {--skip-clear-queue : Ne pas vider la queue avant le scrapping}
        {--skip-notify : Ne pas notifier les admin à la fin}
        {--skip-super-admin-prompt : Ne pas demander la création du super_admin (CI / scripts)}
        {--skip-downloads : Ne pas compiler le livre de règles (PDF / ODT)}
        {--verify : Exécuter project:init:verify à la fin (échec si socle incomplet)}
        {--verify-with-rules : Comme --verify avec contrôle des pages règles CMS}
        '.YesNoFlags::SIGNATURE;

    protected $description = 'Initialise le projet (migrations, seeders, capacités locales, puis types/scrapping DofusDB)';

    /** Ordre des entités scrapping (dépendances). */
    private const SCRAPPING_ENTITIES = [
        'class',      // breeds
        'spell',
        'monster',
        'resource',
        'consumable',
        'item',
        'panoply',
    ];

    /** Tranches de niveau pour monstres (éviter timeouts). */
    private const MONSTER_LEVEL_CHUNK = 50;

    public function handle(): int
    {
        if (! $this->guardNotProduction(
            'project:init est interdit en production (migrations/seeders/scrapping massifs). '
            .'Utilisez des pipelines de déploiement et des migrations ciblées.'
        )) {
            return ArtisanExitCode::FAILURE;
        }

        if ($this->abortIfConflictingYesNoFlags()) {
            return ArtisanExitCode::FAILURE;
        }

        if (($overwriteAbort = $this->confirmSeedOverwriteOrAbort()) !== null) {
            return $overwriteAbort;
        }

        set_time_limit(0);
        $startedAt = microtime(true);
        $phaseStatuses = [
            'migrations' => 'pending',
            'storage_link' => 'pending',
            'seeders' => 'pending',
            'rules_import' => 'pending',
            'capabilities' => 'pending',
            'types' => 'pending',
            'scrapping' => 'pending',
            'breed_sections' => 'pending',
            'bibliotheque_pages' => 'pending',
            'rules_downloads' => 'pending',
            'scheduler' => 'pending',
        ];

        $this->info('=== Initialisation du projet KrosmozJDR ===');
        $this->newLine();

        if ((bool) $this->option('deps')) {
            $this->info('Phase 0 : dépendances (project:deps — composer + pnpm + optimize)');
            $code = $this->call('project:deps', array_merge(
                ['--all' => true],
                $this->yesNoCallOptions()
            ));
            if ($code !== 0) {
                $this->error('Échec de project:deps.');

                return ArtisanExitCode::FAILURE;
            }
            $this->newLine();
        }

        $success = false;
        $lastError = null;

        try {
            if (! (bool) $this->option('skip-migrate')) {
                $this->runMigrations();
                $phaseStatuses['migrations'] = 'ok';
            } else {
                $this->warn('Migrations ignorées (--skip-migrate).');
                $phaseStatuses['migrations'] = 'skipped';
            }
            $this->newLine();

            $this->runStorageLink();
            $phaseStatuses['storage_link'] = $this->runStorageLink() ? 'ok' : 'warn';
            $this->newLine();

            if (! (bool) $this->option('skip-seeders')) {
                $phaseStatuses['seeders'] = $this->runSeeders() ? 'ok' : 'warn';
                $phaseStatuses['rules_import'] = $this->runRulesPagesImport() ? 'ok' : 'warn';
            } else {
                $this->warn('Seeders ignorés (--skip-seeders).');
                $this->warn('Import des règles ignoré (dépend de la création des pages/sections seedées).');
                $phaseStatuses['seeders'] = 'skipped';
                $phaseStatuses['rules_import'] = 'skipped';
            }
            $this->newLine();

            if ((bool) $this->option('skip-downloads')) {
                $this->warn('Compilation du livre de règles ignorée (--skip-downloads).');
                $phaseStatuses['rules_downloads'] = 'skipped';
            } else {
                $phaseStatuses['rules_downloads'] = $this->runRulesDownloadsCompile() ? 'ok' : 'warn';
            }
            $this->newLine();

            if (! (bool) $this->option('skip-capabilities')) {
                $phaseStatuses['capabilities'] = $this->runCapabilitiesImport() ? 'ok' : 'warn';
            } else {
                $this->warn('Capabilities ignorées (--skip-capabilities).');
                $phaseStatuses['capabilities'] = 'skipped';
            }
            $this->newLine();

            if (! (bool) $this->option('skip-types')) {
                $phaseStatuses['types'] = $this->runTypesSetup() ? 'ok' : 'warn';
            } else {
                $this->warn('Types DofusDB ignorés (--skip-types).');
                $phaseStatuses['types'] = 'skipped';
            }
            $this->newLine();

            if (! (bool) $this->option('skip-scrapping')) {
                $phaseStatuses['scrapping'] = $this->runScrapping() ? 'ok' : 'warn';
            } else {
                $this->warn('Scrapping entités ignoré (--skip-scrapping).');
                $phaseStatuses['scrapping'] = 'skipped';
            }
            $this->newLine();

            $phaseStatuses['bibliotheque_pages'] = $this->runBibliothequeEntityPagesSyncPhase() ? 'ok' : 'warn';
            $this->newLine();

            if (! (bool) $this->option('skip-breeds')) {
                $phaseStatuses['breed_sections'] = $this->runBreedSectionsSeeder() ? 'ok' : 'warn';
            } else {
                $this->warn('Sections classes ignorées (--skip-breeds).');
                $phaseStatuses['breed_sections'] = 'skipped';
            }
            $this->newLine();

            if ((bool) $this->option('init-scheduler')) {
                $this->runInitScheduler();
                $phaseStatuses['scheduler'] = 'ok';
            } else {
                $phaseStatuses['scheduler'] = 'skipped';
            }
            $this->newLine();

            $success = true;

            if ((bool) $this->option('verify') || (bool) $this->option('verify-with-rules')) {
                $verifyArgs = [];
                if ((bool) $this->option('verify-with-rules')) {
                    $verifyArgs['--with-rules'] = true;
                }
                $verifyCode = $this->call('project:init:verify', $verifyArgs);
                if ($verifyCode !== 0) {
                    $success = false;
                    $lastError = 'project:init:verify a échoué.';
                }
            }
        } catch (Throwable $e) {
            foreach ($phaseStatuses as $phase => $status) {
                if ($status === 'pending') {
                    $phaseStatuses[$phase] = 'not_run';
                }
            }
            $lastError = $e->getMessage();
            throw $e;
        } finally {
            SeedMode::clearForce();
            $duration = microtime(true) - $startedAt;
            $finishedAt = now()->format('d/m/Y à H:i:s');
            $this->printInitSummary($phaseStatuses, $success, $duration, $finishedAt, $lastError);
            if (! (bool) $this->option('skip-notify')) {
                try {
                    NotificationService::notifyProjectMaintenance(
                        'init',
                        $success,
                        $duration,
                        $finishedAt,
                        $lastError,
                    );
                } catch (Throwable $notifyError) {
                    $this->warn('Notification maintenance ignorée : '.$notifyError->getMessage());
                }
            }
        }

        $this->info('=== Initialisation terminée ===');

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * @param  array<string, string>  $phaseStatuses
     */
    private function printInitSummary(
        array $phaseStatuses,
        bool $success,
        float $duration,
        string $finishedAt,
        ?string $lastError
    ): void {
        $this->info('=== Récapitulatif initialisation ===');
        $labels = [
            'migrations' => 'Migrations',
            'storage_link' => 'Storage link',
            'seeders' => 'Seeders',
            'rules_import' => 'Import règles CMS',
            'rules_downloads' => 'Compilation livre PDF/ODT',
            'capabilities' => 'Capabilities (fichier local)',
            'types' => 'Types DofusDB (API)',
            'scrapping' => 'Scrapping entités (API)',
            'bibliotheque_pages' => 'Sous-pages bibliothèque (classes / spé.)',
            'scheduler' => 'Scheduler',
        ];

        foreach ($labels as $key => $label) {
            $status = $phaseStatuses[$key] ?? 'unknown';
            $badge = match ($status) {
                'ok' => '<info>OK</info>',
                'warn' => '<comment>WARN</comment>',
                'skipped' => '<comment>SKIP</comment>',
                'not_run' => '<fg=gray>NON LANCÉ</>',
                default => '<error>ERREUR</error>',
            };
            $this->line(" - {$label}: {$badge}");
        }

        $global = $success ? '<info>SUCCÈS</info>' : '<error>ÉCHEC</error>';
        $this->line('Statut global : '.$global);
        $this->line('Durée : '.number_format($duration, 1, ',', ' ').' s');
        $this->line('Fin : '.$finishedAt);
        if ($lastError !== null && trim($lastError) !== '') {
            $this->line('Dernière erreur : '.$lastError);
        }
        $this->newLine();
    }

    private function runStorageLink(): bool
    {
        $this->info('Phase 1b : Lien symbolique storage');
        $this->line('  → storage:link');
        $code = Artisan::call('storage:link');
        $this->output->write(Artisan::output());

        if ($code !== 0) {
            $this->warn('  Avertissement : storage:link a remonté un code non nul (souvent lien déjà existant).');

            return false;
        }

        return true;
    }

    private function runMigrations(): void
    {
        $this->info('Phase 1 : Migrations');
        $cmd = (bool) $this->option('fresh') ? 'migrate:fresh' : 'migrate';
        $this->line("  → php artisan {$cmd} --force");
        $code = Artisan::call($cmd, ['--force' => true]);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            throw new \RuntimeException("Échec de {$cmd}.");
        }
    }

    private function runSeeders(): bool
    {
        $this->info('Phase 2 : Seeders');
        $hasWarnings = false;

        $this->line('  → scrapping:setup (socle scrapping)');
        $code = Artisan::call('scrapping:setup', [
            '--skip-migrate' => true,
            '--fresh' => false,
        ]);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            throw new \RuntimeException('Échec de scrapping:setup.');
        }

        $seeders = [
            UserSeeder::class,
            CriticalPagesSeeder::class,
            NavMenuSeeder::class,
            PageSeeder::class,
            SectionSeeder::class,
            SubEffectSeeder::class,
            // Alignement avec {@see DatabaseSeeder} (hors TypeSeeder / caractéristiques déjà dans scrapping:setup)
            LanguageSeeder::class,
            ConditionSeeder::class,
            CreatureTraitSeeder::class,
            CreationPagesSeeder::class,
            // Étalons d'équipement versionnés. Ils portent auto_update = false : le scrapping
            // (phase 6) ne les écrase pas.
            ItemSeeder::class,
            PanoplySeeder::class,
            ConsumableSeeder::class,
            // Fiches des 19 classes, passifs, invocations, puis 24 sorts de classe (liaison breed_spell).
            ClassBreedSeeder::class,
            CapabilitySeeder::class,
            MonsterSeeder::class,
            SpellSeeder::class,
        ];
        foreach ($seeders as $seeder) {
            $this->line("  → {$seeder}");
            $code = Artisan::call('db:seed', ['--class' => $seeder, '--force' => true]);
            $this->output->write(Artisan::output());
            if ($code !== 0) {
                $this->warn("  Avertissement : échec partiel de {$seeder}");
                $hasWarnings = true;
            } elseif ($seeder === UserSeeder::class) {
                $this->runPrimarySuperAdminPrompt();
            }
        }

        if (! (bool) $this->option('skip-specializations')) {
            $this->line('  → '.SpecializationSeeder::class.' (HTML legacy si présents, plus brouillons manquants)');
            $code = Artisan::call('db:seed', ['--class' => SpecializationSeeder::class, '--force' => true]);
            $this->output->write(Artisan::output());
            if ($code !== 0) {
                $this->warn('  Avertissement : seeder spécialisations legacy en échec (migrations ou fichiers manquants).');
                $hasWarnings = true;
            }
        } else {
            $this->warn('  Spécialisations legacy ignorées (--skip-specializations).');
        }

        $this->line('  → '.NpcSeeder::class.' (PNJ Incarnam, après items / classes / sorts / spe)');
        $code = Artisan::call('db:seed', ['--class' => NpcSeeder::class, '--force' => true]);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : échec partiel de '.NpcSeeder::class);
            $hasWarnings = true;
        }

        // MonsterRaceSeeder est inclus dans TypeSeeder (scrapping:setup)
        return ! $hasWarnings;
    }

    /**
     * Importe la table des matières des règles dans les pages CMS pour un projet initialisé "clé en main".
     */
    private function runRulesPagesImport(): bool
    {
        $this->info('Phase 2b : Import des règles (TABLE_DES_MATIERES.md → pages CMS)');
        $this->line('  → pages:import-rules-toc (pages règles CMS)');
        $code = Artisan::call('pages:import-rules-toc');
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : import des pages règles échoué.');
            $this->warn('  Vérifiez le fichier TABLE_DES_MATIERES.md et les logs de pages:import-rules-toc.');

            return false;
        }

        $this->info('  ✅ Import des règles terminé.');

        return true;
    }

    /**
     * Compile PDF et ODT du livre pour la page Ressources.
     */
    private function runRulesDownloadsCompile(): bool
    {
        $this->info('Phase 2c : Compilation du livre de règles (PDF / ODT)');
        $this->line('  → rules:compile-downloads');
        $code = Artisan::call('rules:compile-downloads');
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : compilation du livre de règles échouée.');
            $this->warn('  Relance : php artisan rules:compile-downloads');

            return false;
        }

        $this->info('  ✅ Livre de règles compilé.');

        return true;
    }

    private function runTypesSetup(): bool
    {
        $this->info('Phase 4 : Types DofusDB (API — ressources, races, types de sorts)');
        $hasWarnings = false;

        $typeArgs = ['--skip-cache' => (bool) $this->option('skip-cache')];

        $this->line('  → scrapping:types:seed (ressources, consommables, équipements)');
        $code = Artisan::call('scrapping:types:seed', $typeArgs);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : seed types item a échoué.');

            return false;
        }

        $this->line('  → scrapping:races:seed (races monstres)');
        $code = Artisan::call('scrapping:races:seed', $typeArgs);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : seed races monstres a échoué.');
            $hasWarnings = true;
        }

        $this->line('  → SpellTypeSeeder (types de sorts, référentiel métier)');
        $code = Artisan::call('db:seed', ['--class' => SpellTypeSeeder::class, '--force' => true]);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : seed types de sorts a échoué.');
            $hasWarnings = true;
        }

        $this->line('  Types récupérés : ressources, consommables, équipements, races monstres, types de sorts');

        return ! $hasWarnings;
    }

    private function runScrapping(): bool
    {
        $this->info('Phase 5 : Scrapping entités DofusDB (le plus long — classes, sorts, monstres, …)');
        $hasWarnings = false;
        DB::reconnect();

        if (! (bool) $this->option('skip-clear-queue')) {
            $this->clearQueue();
        }

        $entityFilter = (string) $this->option('entity');
        $entities = $entityFilter !== ''
            ? $this->normalizeEntityCsvToList($entityFilter)
            : self::SCRAPPING_ENTITIES;

        $maxItems = max(0, (int) $this->option('max-items'));
        $noImage = (bool) $this->option('noimage');
        $simulate = (bool) $this->option('simulate');

        $scrapArgs = [
            '--max-items' => $maxItems,
            '--limit' => 100,
            '--max-pages' => 0,
            '--update-mode' => (string) $this->option('update-mode'),
            '--skip-existing' => true,
        ];
        if ($noImage) {
            $scrapArgs['--noimage'] = true;
        }
        if ($simulate) {
            $scrapArgs['--simulate'] = true;
        }
        if ((bool) $this->option('skip-cache')) {
            $scrapArgs['--skip-cache'] = true;
        }

        foreach ($entities as $entity) {
            $entity = strtolower(trim($entity));
            if (! in_array($entity, self::SCRAPPING_ENTITIES, true)) {
                $this->warn("  Entité inconnue ignorée : {$entity}");
                $hasWarnings = true;

                continue;
            }

            if ($entity === 'monster') {
                if (! $this->runScrappingMonsters($scrapArgs)) {
                    $hasWarnings = true;
                }
                $this->newLine();

                continue;
            }
            if ($entity === 'resource') {
                $this->line('  → scrapping:run --entity=resource --resource-types=allowed');
                $code = $this->call('scrapping:run', array_merge($scrapArgs, [
                    '--entity' => 'resource',
                    '--resource-types' => 'allowed',
                    '--max-pages' => 0,
                ]));
            } else {
                $this->line("  → scrapping:run --entity={$entity}");
                $code = $this->call('scrapping:run', array_merge($scrapArgs, [
                    '--entity' => $entity,
                ]));
            }
            if ($code !== 0) {
                $this->warn("  Avertissement : scrapping {$entity} a échoué.");
                $hasWarnings = true;
            }
            DB::reconnect();
            $this->newLine();
        }

        return ! $hasWarnings;
    }

    private function runScrappingMonsters(array $baseArgs): bool
    {
        $maxLevel = 250;
        $chunk = self::MONSTER_LEVEL_CHUNK;
        $hasWarnings = false;

        for ($min = 1; $min <= $maxLevel; $min += $chunk) {
            $max = min($min + $chunk - 1, $maxLevel);
            $this->line("  → scrapping:run --entity=monster --levelMin={$min} --levelMax={$max}");
            $code = $this->call('scrapping:run', array_merge($baseArgs, [
                '--entity' => 'monster',
                '--levelMin' => (string) $min,
                '--levelMax' => (string) $max,
            ]));
            if ($code !== 0) {
                $this->warn("  Avertissement : scrapping monster niveau {$min}-{$max} a échoué.");
                $hasWarnings = true;
            }
            DB::reconnect();
        }

        return ! $hasWarnings;
    }

    private function runCapabilitiesImport(): bool
    {
        $this->info('Phase 3 : Capabilities (import local, sans appel DofusDB)');
        $path = base_path('database/seeders/data/capability.json');
        if (! is_file($path)) {
            $this->line('  Fichier capability.json absent, import ignoré.');

            return true;
        }
        $this->line("  → capabilities:import-legacy {$path}");
        $code = Artisan::call('capabilities:import-legacy', [
            'file' => $path,
        ]);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : import capabilities a échoué.');

            return false;
        }

        return true;
    }

    private function runBibliothequeEntityPagesSyncPhase(): bool
    {
        $this->info('Phase 5b : Sous-pages bibliothèque (classes et spécialisations)');

        return $this->runBibliothequeEntityPagesSync();
    }

    private function runBreedSectionsSeeder(): bool
    {
        $this->info('Phase 5c : Sections CMS des classes (BreedSeeder — HTML legacy ou colonnes scrappées)');
        $this->line('  → '.BreedSeeder::class);
        $code = Artisan::call('db:seed', ['--class' => BreedSeeder::class, '--force' => true]);
        $this->output->write(Artisan::output());

        return $code === 0;
    }

    private function runInitScheduler(): void
    {
        $this->info('Phase 6 : Initialisation du scheduler (cron)');

        $path = base_path();
        $php = defined('PHP_BINARY') ? PHP_BINARY : 'php';
        $cronLine = "* * * * * cd {$path} && {$php} artisan schedule:run >> /dev/null 2>&1";

        $this->line('  Pour que le scheduler Laravel soit exécuté, ajoutez cette ligne à la crontab :');
        $this->newLine();
        $this->line("    <fg=green>{$cronLine}</>");
        $this->newLine();
        $this->line('  Commande : <fg=cyan>crontab -e</> puis coller la ligne ci-dessus.');
        $this->line('  Pour project:data sync planifié : définissez PROJECT_UPDATE_AUTO_ENABLED=true et PROJECT_UPDATE_CRON dans .env');
        $this->newLine();
        $this->line('  Tâches planifiées actuelles :');
        Artisan::call('schedule:list');
        $this->output->write(Artisan::output());
    }

    private function clearQueue(): void
    {
        $connection = Config::get('queue.default');
        if ($connection === 'sync') {
            return;
        }
        $this->line('  → Nettoyage de la queue (jobs en attente + failed)');
        Artisan::call('queue:clear', [$connection, '--force' => true]);
        Artisan::call('queue:flush');
        $this->output->write(Artisan::output());
    }
}
