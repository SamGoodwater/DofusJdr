<?php

declare(strict_types=1);

namespace App\Console\Commands\Characteristics;

use App\Console\ArtisanExitCode;
use App\Models\CharacteristicConsumable;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use App\Services\Characteristic\Norms\NormsBudgetFormulaFitter;
use App\Services\Characteristic\Norms\NormsGridGenerator;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Ajuste normes de budget (bonus) en formule neutre + coefficients de palier.
 *
 * @example php artisan characteristics:fit-norms-budget-formulas --dry-run
 * @example php artisan characteristics:fit-norms-budget-formulas --group=spell
 */
final class FitCharacteristicNormsBudgetFormulasCommand extends Command
{
    protected $signature = 'characteristics:fit-norms-budget-formulas
        {--dry-run : Affiche sans écrire}
        {--group= : spell|consumable|object (défaut : les trois)}
        {--force : Réécrit aussi les formules déjà sans [power]}';

    protected $description = 'Ajuste les barèmes de budget (±) en formule + coefficients (comme dommages_spell)';

    public function handle(NormsBudgetFormulaFitter $fitter, NormsGridGenerator $generator): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $groupOpt = $this->option('group');
        $groups = is_string($groupOpt) && $groupOpt !== ''
            ? [strtolower($groupOpt)]
            : ['spell', 'consumable', 'object'];

        $written = 0;
        $skipped = 0;
        $skippedEmpty = [];
        $report = [];

        /** @var array<string, array{formula: string, coefficients: array<string, float>}> $spellFits */
        $spellFits = [];

        foreach ($groups as $group) {
            foreach ($this->loadPivots($group) as $pivot) {
                $key = (string) ($pivot->characteristic?->key ?? '');
                $entity = (string) $pivot->entity;
                $label = "{$group}|{$entity}|{$key}";

                if (! $fitter->isEligiblePivot($pivot)) {
                    $skipped++;

                    continue;
                }

                $existing = is_string($pivot->norms_formula) ? $pivot->norms_formula : null;
                if (! $force && ! $fitter->shouldOverwriteExisting($existing)) {
                    $skipped++;

                    continue;
                }

                // Réutilise la formule sort pour le consommable de même stem si disponible
                $fit = null;
                if ($group === 'consumable') {
                    $stem = preg_replace('/_consumable$/', '', $key) ?: $key;
                    $spellKey = $stem.'_spell';
                    if (isset($spellFits[$spellKey])) {
                        $fit = [
                            'formula' => $spellFits[$spellKey]['formula'],
                            'coefficients' => $spellFits[$spellKey]['coefficients'],
                            'mae' => 0.0,
                        ];
                    }
                }

                if ($fit === null) {
                    $grid = is_array($pivot->norms_grid) ? $pivot->norms_grid : [];
                    $fit = $fitter->fitGrid($grid);
                }

                if ($fit === null) {
                    $skipped++;
                    $skippedEmpty[] = $label;

                    continue;
                }

                $report[] = sprintf(
                    '%s → %s coefs=%s mae=%s',
                    $label,
                    $fit['formula'],
                    json_encode($fit['coefficients'], JSON_UNESCAPED_UNICODE),
                    (string) $fit['mae']
                );

                if ($group === 'spell') {
                    $spellFits[$key] = [
                        'formula' => $fit['formula'],
                        'coefficients' => $fit['coefficients'],
                    ];
                }

                if ($dryRun) {
                    $written++;

                    continue;
                }

                $pivot->norms_formula = $fit['formula'];
                $pivot->norms_power_coefficients = $fit['coefficients'];
                $pivot->save();
                $generator->regenerateKey($key, $entity);
                $written++;
            }
        }

        foreach ($report as $line) {
            $this->line($line);
        }

        if ($skippedEmpty !== []) {
            $this->warn('Grilles quasi vides (conservées manuelles) :');
            foreach ($skippedEmpty as $line) {
                $this->line('  · '.$line);
            }
        }

        $this->info(($dryRun ? '[dry-run] ' : '')."{$written} ligne(s) ajustée(s), {$skipped} ignorée(s).");

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * @return Collection<int, Model>
     */
    private function loadPivots(string $group): Collection
    {
        return match ($group) {
            'spell' => CharacteristicSpell::query()->with('characteristic')->where('entity', '*')->get(),
            'consumable' => CharacteristicConsumable::query()->with('characteristic')->where('entity', '*')->get(),
            'object' => CharacteristicObject::query()->with('characteristic')->where('entity', '*')->get(),
            default => collect(),
        };
    }
}
