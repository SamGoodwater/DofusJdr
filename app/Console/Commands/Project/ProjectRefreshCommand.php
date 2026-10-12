<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\AcceptsYesNoFlags;
use App\Console\YesNoFlags;
use App\Services\Project\ProjectClearService;
use Illuminate\Console\Command;

/**
 * Réinstallation locale : grand ménage puis pipeline `project:init --fresh`.
 * Interdit en production.
 *
 * @example php artisan project:refresh --fast --force
 * @example php artisan project:refresh --fast -y
 */
class ProjectRefreshCommand extends Command
{
    use AcceptsYesNoFlags;

    public function __construct(
        private readonly ProjectClearService $projectClearService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:refresh
        {--hard : Exécuter setup --refresh (vendor + node_modules) avant le pipeline d’init}
        {--without-seed : Transmet --skip-seeders à project:init}
        {--overwrite : DANGER — transmet --overwrite à project:init (réécrit les lignes seedées ; confirmation + CI : -y)}
        {--skip-scrapping : Transmet à project:init}
        {--fast : --skip-scrapping et --skip-types (données locales sans DofusDB)}
        {--noimage : Transmet à project:init}
        {--skip-types : Transmet à project:init}
        {--force : Ne pas demander confirmation ; transmet --skip-super-admin-prompt à project:init}
        '.YesNoFlags::SIGNATURE;

    protected $description = 'Réinit locale : grand ménage, setup --hard optionnel, project:init --fresh, puis clear --safe';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Interdit en production.');

            return ArtisanExitCode::FAILURE;
        }

        if ($this->abortIfConflictingYesNoFlags()) {
            return ArtisanExitCode::FAILURE;
        }

        if ($this->wantsNo()) {
            $this->info('Annulé.');

            return ArtisanExitCode::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirmOrFlag(
            'Cette commande exécute migrate:fresh via project:init et détruira toutes les tables. Continuer ?',
            false
        )) {
            $this->info('Annulé.');

            return ArtisanExitCode::FAILURE;
        }

        $this->info('→ Grand ménage local');
        $code = $this->projectClearService->clearDeep($this);
        if ($code !== 0) {
            return $code;
        }

        if ($this->option('hard')) {
            $this->info('→ setup --refresh');
            $code = $this->call('setup', array_merge(
                ['--refresh' => true],
                $this->yesNoCallOptions()
            ));
            if ($code !== 0) {
                return $code;
            }
        }

        $this->info('→ project:init --fresh');
        $code = $this->call('project:init', $this->buildProjectInitArguments());
        if ($code !== 0) {
            return $code;
        }

        $this->info('→ project:clear --safe');
        $code = $this->projectClearService->clearSafe($this);
        if ($code !== 0) {
            return $code;
        }

        $this->info('project:refresh terminé.');

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * @return array<string, bool|string>
     */
    private function buildProjectInitArguments(): array
    {
        $arguments = [
            '--fresh' => true,
        ];

        if ($this->option('force') || $this->wantsYes() || ! $this->input->isInteractive()) {
            $arguments['--skip-super-admin-prompt'] = true;
        }

        if ($this->option('without-seed')) {
            $arguments['--skip-seeders'] = true;
        }

        if ($this->option('fast')) {
            $arguments['--skip-scrapping'] = true;
            $arguments['--skip-types'] = true;
        }

        foreach (['overwrite', 'skip-scrapping', 'noimage', 'skip-types'] as $name) {
            if ($this->option($name)) {
                $arguments['--'.$name] = true;
            }
        }

        return array_merge($arguments, $this->yesNoCallOptions());
    }
}
