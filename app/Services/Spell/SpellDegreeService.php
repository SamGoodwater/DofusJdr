<?php

declare(strict_types=1);

namespace App\Services\Spell;

use App\Models\Entity\Spell;
use App\Models\SpellDegree;
use App\Services\Effect\EffectTextSanitizer;
use Illuminate\Support\Facades\DB;

/**
 * CRUD transactionnel des degrés et effets d’un sort.
 */
final class SpellDegreeService
{
    public function __construct(
        private readonly SpellDegreeResolver $resolver
    ) {}

    /**
     * Crée un degré en copiant les propriétés et effets du précédent (pré-remplissage éditable).
     *
     * @param  array<string, mixed>  $overrides
     */
    public function createDegree(Spell $spell, array $overrides = []): SpellDegree
    {
        return DB::transaction(function () use ($spell, $overrides): SpellDegree {
            $spell->loadMissing('degrees');
            $orderedDegrees = $spell->degrees->sortBy('position');
            $previous = $orderedDegrees->isEmpty() ? null : $orderedDegrees->last();
            $position = (int) ($previous !== null ? $previous->position : 0) + 1;

            $props = $this->resolver->resolveProperties($spell, $previous);
            $payload = [
                'spell_id' => $spell->id,
                'position' => $position,
                'required_level' => $overrides['required_level']
                    ?? (($previous !== null ? ($previous->required_level ?? 0) : 0) + 1),
                // Nouveau degré = copie matérialisée éditable (pas d’héritage d’effets par défaut).
                'inherits_effects' => array_key_exists('inherits_effects', $overrides)
                    ? (bool) $overrides['inherits_effects']
                    : false,
                'properties_source' => array_key_exists('properties_source', $overrides)
                    ? $this->normalizePropertiesSource($overrides['properties_source'])
                    : ($previous !== null
                        ? ($previous->properties_source ?: SpellDegree::PROPERTIES_SOURCE_OWN)
                        : SpellDegree::PROPERTIES_SOURCE_OWN),
            ];

            foreach (SpellDegree::PROPERTY_KEYS as $key) {
                if (array_key_exists($key, $overrides)) {
                    $payload[$key] = $overrides[$key];
                } else {
                    $payload[$key] = $props[$key] ?? null;
                }
            }

            $degree = SpellDegree::query()->create($payload);

            $shouldCopyEffects = ! $degree->inherits_effects
                && $previous !== null
                && empty($overrides['skip_copy_effects']);
            if ($shouldCopyEffects) {
                $this->copyEffectsFromSource($spell, $previous, $degree);
            }

            return $degree->fresh(['effects.subEffect']) ?? $degree;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateDegree(SpellDegree $degree, array $data): SpellDegree
    {
        return DB::transaction(function () use ($degree, $data): SpellDegree {
            $allowed = array_merge(
                ['required_level', 'inherits_effects', 'properties_source'],
                SpellDegree::PROPERTY_KEYS
            );
            $payload = [];
            foreach ($allowed as $key) {
                if (! array_key_exists($key, $data)) {
                    continue;
                }
                $payload[$key] = $key === 'properties_source'
                    ? $this->normalizePropertiesSource($data[$key])
                    : $data[$key];
            }

            if (array_key_exists('inherits_effects', $payload) && $payload['inherits_effects'] === true) {
                $degree->effects()->delete();
            }

            $degree->update($payload);

            return $degree->fresh(['effects.subEffect']) ?? $degree;
        });
    }

    /**
     * Synchronise plusieurs degrés en une transaction (enregistrement unique UI).
     *
     * @param  list<array<string, mixed>>  $degreesPayload
     */
    public function syncDegreesBulk(Spell $spell, array $degreesPayload): Spell
    {
        return DB::transaction(function () use ($spell, $degreesPayload): Spell {
            $spell->loadMissing('degrees');
            $byId = $spell->degrees->keyBy('id');

            foreach ($degreesPayload as $item) {
                $id = isset($item['id']) ? (int) $item['id'] : 0;
                if ($id <= 0 || ! $byId->has($id)) {
                    continue;
                }
                /** @var SpellDegree $degree */
                $degree = $byId->get($id);
                $effects = $item['effects'] ?? null;
                unset($item['effects'], $item['id'], $item['position'], $item['rows']);

                $this->updateDegree($degree, $item);
                if (is_array($effects)) {
                    $this->syncEffects($degree->fresh() ?? $degree, $effects);
                }
            }

            return $spell->fresh(['degrees.effects.subEffect']) ?? $spell;
        });
    }

    /**
     * Matérialise les effets hérités pour permettre l’édition locale.
     */
    public function materializeEffects(SpellDegree $degree): SpellDegree
    {
        return DB::transaction(function () use ($degree): SpellDegree {
            $spell = $degree->spell()->with('degrees.effects.subEffect')->firstOrFail();
            if (! $degree->inherits_effects) {
                return $degree->fresh(['effects.subEffect']) ?? $degree;
            }

            $source = $this->resolver->effectsSourceDegree($spell, $degree);
            $degree->update(['inherits_effects' => false]);
            $degree->effects()->delete();

            if ($source !== null && $source->id !== $degree->id) {
                $this->copyEffectsFromSource($spell, $source, $degree);
            }

            return $degree->fresh(['effects.subEffect']) ?? $degree;
        });
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    public function syncEffects(SpellDegree $degree, array $rows): SpellDegree
    {
        return DB::transaction(function () use ($degree, $rows): SpellDegree {
            if ($degree->inherits_effects) {
                $this->materializeEffects($degree);
                $degree->refresh();
            }

            $degree->effects()->delete();
            $sanitizer = new EffectTextSanitizer;
            foreach ($rows as $i => $row) {
                $params = $row['params'] ?? null;
                if (is_array($params)) {
                    foreach (['value_formula', 'value_formula_crit', 'life_steal_formula', 'cells_formula'] as $fk) {
                        if (! empty($params[$fk]) && is_string($params[$fk])) {
                            $params[$fk] = $sanitizer->sanitize($params[$fk]);
                        }
                    }
                }
                $duration = $row['duration_formula'] ?? null;
                if (is_string($duration) && $duration !== '') {
                    $duration = $sanitizer->sanitize($duration);
                }
                $logicCondition = $row['logic_condition'] ?? null;
                if (is_string($logicCondition) && $logicCondition !== '') {
                    $logicCondition = $sanitizer->sanitize($logicCondition);
                }

                $degree->effects()->create([
                    'sub_effect_id' => $row['sub_effect_id'],
                    'order' => $row['order'] ?? $i,
                    'scope' => $row['scope'] ?? 'general',
                    'value_min' => $row['value_min'] ?? null,
                    'value_max' => $row['value_max'] ?? null,
                    'dice_num' => $row['dice_num'] ?? null,
                    'dice_side' => $row['dice_side'] ?? null,
                    'duration_formula' => $duration,
                    'logic_group' => $row['logic_group'] ?? null,
                    'logic_operator' => $row['logic_operator'] ?? null,
                    'logic_condition' => $logicCondition,
                    'crit_only' => (bool) ($row['crit_only'] ?? false),
                    'params' => $params,
                ]);
            }

            return $degree->fresh(['effects.subEffect']) ?? $degree;
        });
    }

    public function deleteDegree(SpellDegree $degree): void
    {
        DB::transaction(function () use ($degree): void {
            $spellId = $degree->spell_id;
            $this->preserveInheritedEffectsBeforeDelete($degree);
            $degree->delete();
            $this->renumberPositions($spellId);
        });
    }

    /**
     * Si le degré supprimé porte les effets, les copies sur le suivant qui en héritait.
     * Sans ça, les degrés restants `inherits_effects` n’ont plus de source et affichent une fiche vide.
     */
    private function preserveInheritedEffectsBeforeDelete(SpellDegree $degree): void
    {
        if ($degree->inherits_effects) {
            return;
        }

        $spell = Spell::query()->with('degrees')->find($degree->spell_id);
        if ($spell === null) {
            return;
        }

        $ordered = $spell->degrees->sortBy('position')->values();
        $idx = $ordered->search(fn (SpellDegree $candidate): bool => $candidate->id === $degree->id);
        if ($idx === false) {
            return;
        }

        $next = $ordered->get((int) $idx + 1);
        if ($next instanceof SpellDegree && $next->inherits_effects) {
            $this->materializeEffects($next);
        }
    }

    public function renumberPositions(int $spellId): void
    {
        $degrees = SpellDegree::query()
            ->where('spell_id', $spellId)
            ->orderBy('position')
            ->orderBy('id')
            ->get();
        $pos = 1;
        foreach ($degrees as $degree) {
            if ((int) $degree->position !== $pos) {
                $degree->update(['position' => $pos]);
            }
            $pos++;
        }
    }

    private function copyEffectsFromSource(Spell $spell, SpellDegree $source, SpellDegree $target): void
    {
        $rows = $this->resolver->resolveEffects($spell, $source);
        foreach ($rows as $i => $row) {
            $target->effects()->create([
                'sub_effect_id' => $row->sub_effect_id,
                'order' => $row->order ?? $i,
                'scope' => $row->scope ?? 'general',
                'value_min' => $row->value_min,
                'value_max' => $row->value_max,
                'dice_num' => $row->dice_num,
                'dice_side' => $row->dice_side,
                'duration_formula' => $row->duration_formula,
                'logic_group' => $row->logic_group,
                'logic_operator' => $row->logic_operator,
                'logic_condition' => $row->logic_condition,
                'crit_only' => (bool) $row->crit_only,
                'params' => $row->params,
            ]);
        }
    }

    /**
     * Propriétés du sort à copier vers un premier degré.
     *
     * @return array<string, mixed>
     */
    public function propertiesFromSpell(Spell $spell): array
    {
        $out = [];
        foreach (SpellDegree::PROPERTY_KEYS as $key) {
            if ($key === 'area') {
                $out[$key] = null;

                continue;
            }
            $out[$key] = $spell->getAttribute($key);
        }

        return $out;
    }

    private function normalizePropertiesSource(mixed $raw): string
    {
        $value = is_string($raw) ? trim($raw) : SpellDegree::PROPERTIES_SOURCE_OWN;
        if (! in_array($value, SpellDegree::PROPERTIES_SOURCES, true)) {
            return SpellDegree::PROPERTIES_SOURCE_OWN;
        }

        return $value;
    }
}
