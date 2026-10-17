<?php

declare(strict_types=1);

namespace App\Services\Condition;

use App\Models\EffectSubEffect;
use App\Models\Entity\Condition;
use App\Models\SpellDegreeEffect;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Recolle les sorts et params d’effets sur les états JDR canoniques.
 *
 * @example
 * $stats = $remapper->remapAll();
 * // ['aliases' => 40, 'spell_links' => 12, 'effect_params' => 20, 'unlinked' => 9000]
 */
final class ConditionSpellCanonicalRemapper
{
    public function __construct(private readonly ConditionCanonicalMapper $mapper) {}

    /**
     * @return array{aliases: int, spell_links: int, unlinked: int, effect_params: int}
     */
    public function remapAll(bool $dryRun = false): array
    {
        $this->mapper->forgetCachedPlayables();

        $aliases = $this->writeCanonicalForeignKeys($dryRun);
        $links = $this->remapSpellLinks($dryRun);
        $params = $this->remapEffectParams($dryRun);

        return [
            'aliases' => $aliases,
            'spell_links' => $links['updated'],
            'unlinked' => $links['deleted'],
            'effect_params' => $params,
        ];
    }

    private function writeCanonicalForeignKeys(bool $dryRun): int
    {
        $updated = 0;
        Condition::query()
            ->whereNotNull('dofusdb_id')
            ->orderBy('id')
            ->chunkById(200, function ($chunk) use ($dryRun, &$updated): void {
                foreach ($chunk as $source) {
                    /** @var Condition $source */
                    $canonical = $this->mapper->resolve($source);
                    $canonicalId = $canonical?->id;
                    if ($canonicalId === $source->id) {
                        $canonicalId = null;
                    }
                    $current = $source->canonical_condition_id !== null ? (int) $source->canonical_condition_id : null;
                    if ($current === $canonicalId) {
                        continue;
                    }
                    $updated++;
                    if (! $dryRun) {
                        $source->canonical_condition_id = $canonicalId;
                        $source->save();
                    }
                }
            });

        return $updated;
    }

    /**
     * @return array{updated: int, deleted: int}
     */
    private function remapSpellLinks(bool $dryRun): array
    {
        $updated = 0;
        $deleted = 0;
        $conditions = Condition::query()->get()->keyBy('id');

        $rows = DB::table('condition_spell')->orderBy('id')->get();
        foreach ($rows as $row) {
            $source = $conditions->get((int) $row->condition_id);
            if ($source === null) {
                $deleted++;
                if (! $dryRun) {
                    DB::table('condition_spell')->where('id', $row->id)->delete();
                }

                continue;
            }

            $canonical = $this->mapper->resolve($source);
            if ($canonical === null) {
                $deleted++;
                if (! $dryRun) {
                    DB::table('condition_spell')->where('id', $row->id)->delete();
                }

                continue;
            }

            if ((int) $canonical->id === (int) $row->condition_id) {
                continue;
            }

            $duplicateQuery = DB::table('condition_spell')
                ->where('spell_id', $row->spell_id)
                ->where('condition_id', $canonical->id)
                ->where('application_mode', $row->application_mode)
                ->where('id', '!=', $row->id);
            if ($row->dofus_effect_id === null) {
                $duplicateQuery->whereNull('dofus_effect_id');
            } else {
                $duplicateQuery->where('dofus_effect_id', $row->dofus_effect_id);
            }
            $duplicate = $duplicateQuery->exists();

            if ($duplicate) {
                $deleted++;
                if (! $dryRun) {
                    DB::table('condition_spell')->where('id', $row->id)->delete();
                }

                continue;
            }

            $updated++;
            if (! $dryRun) {
                DB::table('condition_spell')->where('id', $row->id)->update([
                    'condition_id' => $canonical->id,
                    'updated_at' => now(),
                ]);
            }
        }

        return ['updated' => $updated, 'deleted' => $deleted];
    }

    private function remapEffectParams(bool $dryRun): int
    {
        $conditions = Condition::query()->get()->keyBy('id');

        return $this->remapParamsOn(EffectSubEffect::class, $dryRun, $conditions)
            + $this->remapParamsOn(SpellDegreeEffect::class, $dryRun, $conditions);
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  Collection<int, Condition>  $conditions
     */
    private function remapParamsOn(string $modelClass, bool $dryRun, Collection $conditions): int
    {
        $updated = 0;

        $modelClass::query()
            ->where('params', 'like', '%condition_id%')
            ->orderBy('id')
            ->chunkById(100, function ($chunk) use ($dryRun, $conditions, &$updated): void {
                foreach ($chunk as $pivot) {
                    /** @var Model $pivot */
                    $params = is_array($pivot->getAttribute('params')) ? $pivot->getAttribute('params') : [];
                    $rawId = $params['condition_id'] ?? null;
                    if ($rawId === null || $rawId === '' || ! is_numeric($rawId)) {
                        continue;
                    }

                    $source = $conditions->get((int) $rawId);
                    if ($source === null) {
                        unset($params['condition_id']);
                        $updated++;
                        if (! $dryRun) {
                            $pivot->setAttribute('params', $params);
                            $pivot->save();
                        }

                        continue;
                    }

                    $canonical = $this->mapper->resolve($source);
                    if ($canonical === null) {
                        unset($params['condition_id']);
                        $updated++;
                        if (! $dryRun) {
                            $pivot->setAttribute('params', $params);
                            $pivot->save();
                        }

                        continue;
                    }

                    if ((int) $canonical->id === (int) $rawId && ($params['condition_name'] ?? null) === $canonical->name) {
                        continue;
                    }

                    $params['condition_id'] = $canonical->id;
                    $params['condition_name'] = $canonical->name;
                    $updated++;
                    if (! $dryRun) {
                        $pivot->setAttribute('params', $params);
                        $pivot->save();
                    }
                }
            });

        return $updated;
    }
}
