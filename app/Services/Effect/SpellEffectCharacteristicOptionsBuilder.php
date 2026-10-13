<?php

declare(strict_types=1);

namespace App\Services\Effect;

use App\Enums\CharacteristicNature;
use App\Models\Characteristic;

/**
 * Listes de caractéristiques de sort pour l’éditeur d’effets (buff/debuff et valeurs d’action).
 */
final class SpellEffectCharacteristicOptionsBuilder
{
    /** @var list<string> */
    private const EXCLUDED_SPELL_KEYS = [
        'name_spell',
        'level_spell',
        'category_spell',
        'element_spell',
    ];

    /**
     * Clé caractéristique spell → slugs de sous-effet autorisés (action_value).
     *
     * @var array<string, list<string>>
     */
    private const ACTION_VALUE_SUB_EFFECTS = [
        'dommages_spell' => ['frapper'],
        'soin_spell' => ['soigner'],
        'bouclier_spell' => ['protéger'],
        'pv_temporaires_spell' => ['donner-pv-temporaires'],
        'vol_vie_spell' => ['frapper'],
        'movement_distance_spell' => ['déplacer'],
        'teleport_distance_spell' => ['déplacer'],
        'jump_distance_spell' => ['déplacer'],
        'pull_distance_spell' => ['déplacer'],
        'push_distance_spell' => ['déplacer'],
    ];

    /**
     * Buff / débuff / vol (groupe sort, nature buff_debuff).
     *
     * @return list<array{key: string, label: string, category: string, helper: string|null, descriptions: mixed}>
     */
    public function buffDebuffOptions(): array
    {
        return Characteristic::query()
            ->where('group', 'spell')
            ->where('nature', CharacteristicNature::BuffDebuff->value)
            ->whereNotIn('key', self::EXCLUDED_SPELL_KEYS)
            ->whereNull('linked_to_characteristic_id')
            ->orderBy('sort_order')
            ->orderBy('key')
            ->get()
            ->map(fn (Characteristic $c): array => $this->mapSpellCharacteristic($c))
            ->values()
            ->all();
    }

    /**
     * Valeurs d’action (groupe sort, nature action_value) + éléments config pour frapper/soigner.
     *
     * @return list<array{key: string, label: string, category: string, sub_effect_slugs: list<string>, helper: string|null, descriptions: mixed}>
     */
    public function actionValueOptions(): array
    {
        $out = [];
        $rows = Characteristic::query()
            ->where('group', 'spell')
            ->where('nature', CharacteristicNature::ActionValue->value)
            ->whereNull('linked_to_characteristic_id')
            ->orderBy('sort_order')
            ->orderBy('key')
            ->get();

        foreach ($rows as $c) {
            $slugs = self::ACTION_VALUE_SUB_EFFECTS[$c->key] ?? [];
            if ($slugs === []) {
                continue;
            }
            $mapped = $this->mapSpellCharacteristic($c);
            $out[] = array_merge($mapped, ['sub_effect_slugs' => $slugs]);
        }

        foreach (config('effect_sub_effects.characteristics', []) as $entry) {
            if (($entry['category'] ?? '') !== 'element') {
                continue;
            }
            $out[] = [
                'key' => (string) ($entry['key'] ?? ''),
                'label' => (string) ($entry['label'] ?? $entry['key'] ?? ''),
                'category' => 'element',
                'helper' => null,
                'descriptions' => null,
                'sub_effect_slugs' => ['frapper', 'soigner'],
            ];
        }

        return $out;
    }

    /**
     * @return array{key: string, label: string, category: string, helper: string|null, descriptions: mixed}
     */
    private function mapSpellCharacteristic(Characteristic $c): array
    {
        $eff = $c->effectiveCharacteristic();

        return [
            'key' => $c->key,
            'label' => $eff->name ?? $eff->short_name ?? $eff->key,
            'category' => $this->inferCategoryForSpellKey($c->key),
            'helper' => $eff->helper,
            'descriptions' => $eff->descriptions,
        ];
    }

    private function inferCategoryForSpellKey(string $key): string
    {
        if (preg_match('/_(acrobatics|animal_handling|arcana|athletics|deception|history|insight|intimidation|investigation|medicine|nature|perception|performance|persuasion|religion|sleight_of_hand|stealth|survival)_spell$/', $key)) {
            return 'skill';
        }
        if (str_contains($key, 'fixed_damage') || str_starts_with($key, 'res_') || str_contains($key, 'fixed_resistance')) {
            return 'element';
        }
        if (in_array($key, ['action_points_spell', 'movement_points_spell', 'wakfu_reserve_spell', 'range_spell'], true)) {
            return 'resource';
        }

        return 'stat';
    }
}
