<?php

declare(strict_types=1);

namespace App\Services\Seeder\Monster;

use App\Enums\EntityState;
use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\Entity\Capability;
use App\Models\Entity\Condition;
use App\Models\Entity\Creature;
use App\Models\Entity\CreatureTrait;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Models\Type\MonsterRace;
use App\Models\Type\SpellType;
use App\Models\User;
use App\Support\ElementBitmask;

/**
 * Importe le bestiaire JDR (JSON `entities/monsters/*.json`, hors invocations).
 *
 * Idempotent. Upsert sur `official_id` `jdr:bestiary:…`. `auto_update = false`.
 * Ne pose pas `dofusdb_id` (les fiches scrap Dofus restent distinctes).
 *
 * @example $result = app(BestiarySeederImporter::class)->import();
 */
final class BestiarySeederImporter
{
    /**
     * @param  bool  $overwrite  False = ignorer les fiches déjà présentes
     * @return array{
     *     created: list<string>,
     *     updated: list<string>,
     *     skipped: list<string>
     * }
     */
    public function import(?BestiaryCatalog $catalog = null, bool $overwrite = true): array
    {
        if ($catalog !== null) {
            return $this->importCatalog($catalog, $overwrite);
        }

        $parts = [];
        foreach (BestiaryCatalog::loadAll() as $one) {
            $parts[] = $this->importCatalog($one, $overwrite);
        }

        return $this->mergeResults($parts);
    }

    /**
     * @return array{
     *     created: list<string>,
     *     updated: list<string>,
     *     skipped: list<string>
     * }
     */
    private function importCatalog(BestiaryCatalog $catalog, bool $overwrite = true): array
    {
        $created = [];
        $updated = [];
        $skipped = [];

        foreach ($catalog->entries() as $entry) {
            $monster = $this->findMonster($entry['official_id']);
            $wasNew = $monster === null;
            if (! $wasNew && ! $overwrite) {
                $skipped[] = $entry['name'].' : déjà présent, conservation';

                continue;
            }

            $spellIds = [];
            foreach ($entry['spells'] as $spellRow) {
                $spell = $this->syncSpell($entry, $spellRow, $skipped);
                if ($spell !== null) {
                    $spellIds[] = $spell->id;
                }
            }

            $monster ??= new Monster;
            $creature = $monster->creature ?? new Creature;

            $attributes = $this->creatureAttributes($entry, $creature);
            if ($creature->exists && $creature->created_by !== null) {
                unset($attributes['created_by']);
            }
            $creature->fill($attributes);
            $creature->save();

            $monster->fill($this->monsterAttributes($entry, $creature->id));
            $monster->creature_id = $creature->id;
            $monster->save();

            $creature->spells()->sync($spellIds);
            $this->syncTraits($creature, $entry['traits'], $entry['name'], $skipped);
            $this->syncCapabilities($creature, $entry['capabilities'], $entry['name'], $skipped);

            $wasNew ? $created[] = $entry['name'] : $updated[] = $entry['name'];
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    /**
     * @param  list<array{created: list<string>, updated: list<string>, skipped: list<string>}>  $parts
     * @return array{created: list<string>, updated: list<string>, skipped: list<string>}
     */
    private function mergeResults(array $parts): array
    {
        $created = [];
        $updated = [];
        $skipped = [];
        foreach ($parts as $part) {
            array_push($created, ...$part['created']);
            array_push($updated, ...$part['updated']);
            array_push($skipped, ...$part['skipped']);
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    /**
     * @param  array<string, mixed>  $entry
     * @param  array<string, mixed>  $spellRow
     * @param  list<string>  $skipped
     */
    private function syncSpell(array $entry, array $spellRow, array &$skipped): ?Spell
    {
        $spell = Spell::query()->where('official_id', $spellRow['official_id'])->first() ?? new Spell;

        $elementSlug = $spellRow['element'];
        $element = is_string($elementSlug) ? ElementBitmask::fromSlug($elementSlug) : null;

        $effectText = $this->effectText($spellRow);
        $isHeal = $spellRow['kind'] === 'soigner';

        $spell->fill([
            'name' => $spellRow['name'],
            'description' => 'Sort de créature « '.$entry['name'].' ».',
            'effect' => $effectText,
            'level' => (string) $entry['level'],
            'pa' => $spellRow['pa'],
            'po_min' => $spellRow['po_min'],
            'po_max' => $spellRow['po_max'],
            'po_editable' => false,
            'sight_line' => true,
            'cast_per_turn' => '1',
            'cast_per_target' => '0',
            'number_between_two_cast' => '0',
            'cast_in_line' => false,
            'cast_in_diagonal' => false,
            'target_type' => Effect::TARGET_DIRECT,
            'max_stack' => 0,
            'global_cooldown' => 0,
            'element' => $element,
            'category' => Spell::CATEGORY_CREATURE,
            'is_magic' => $spellRow['is_magic'],
            'powerful' => 0,
            'resolution_mode' => $spellRow['resolution_mode'],
            'attack_characteristic_key' => $isHeal ? null : $spellRow['attack_characteristic_key'],
            'save_characteristic_key' => null,
            'save_dc_formula' => null,
            'save_success_note' => null,
            'auto_success_if_willing_target' => $isHeal,
            'allows_reaction' => false,
            'duration' => null,
            'official_id' => $spellRow['official_id'],
            'state' => Spell::STATE_AUTO,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'auto_update' => false,
        ]);
        $spell->save();

        $type = SpellType::findByLabel($spellRow['type'] ?? null);
        if ($type === null) {
            $skipped[] = $entry['name'].' / '.$spellRow['name'].' : type « '.$spellRow['type'].' » introuvable';
        } else {
            $spell->spellTypes()->sync([$type->id]);
        }

        $this->syncSpellEffect($spell, $entry, $spellRow, $effectText, $skipped);

        return $spell;
    }

    /**
     * @param  array<string, mixed>  $entry
     * @param  array<string, mixed>  $spellRow
     * @param  list<string>  $skipped
     */
    private function syncSpellEffect(
        Spell $spell,
        array $entry,
        array $spellRow,
        string $effectText,
        array &$skipped
    ): void {
        $slug = 'jdr-bestiary-'.$entry['key'].'-'.$spellRow['key'];
        if (strlen($slug) > 64) {
            $slug = substr($slug, 0, 64);
        }

        $effect = Effect::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $spellRow['name'],
                'description' => $effectText,
                'target_type' => Effect::TARGET_DIRECT,
            ]
        );
        $effect->fill([
            'name' => $spellRow['name'],
            'description' => $effectText,
            'target_type' => Effect::TARGET_DIRECT,
        ]);
        $effect->save();

        $degree = EffectDegree::query()->firstOrCreate(
            [
                'effect_id' => $effect->id,
                'degree' => 1,
            ],
            [
                'required_creature_level' => $entry['level'],
                'area' => 'point',
                'slug' => $slug.'-d1',
            ]
        );
        $degree->update([
            'required_creature_level' => $entry['level'],
            'area' => 'point',
        ]);
        $degree->effectSubEffects()->delete();

        $order = 0;
        if ($spellRow['kind'] !== 'appliquer-etat') {
            $subId = SubEffect::query()->where('slug', $spellRow['kind'])->value('id');
            if ($subId === null) {
                $skipped[] = $entry['name'].' / '.$spellRow['name'].' : sous-effet « '.$spellRow['kind'].' » introuvable';
            } else {
                $params = $this->primaryParams($spellRow);
                $degree->effectSubEffects()->create([
                    'sub_effect_id' => (int) $subId,
                    'order' => $order,
                    'scope' => Effect::SCOPE_GENERAL,
                    'params' => $params,
                    'crit_only' => false,
                    'duration_formula' => null,
                ]);
                $order++;
            }
        }

        if ($spellRow['condition'] !== null) {
            $this->attachConditionSubEffect($degree, $spellRow['condition'], $order, $entry['name'], $spellRow['name'], $skipped);
        }

        $spell->effects()->sync([$effect->id]);
    }

    /**
     * @param  array<string, mixed>  $spellRow
     * @return array<string, mixed>
     */
    private function primaryParams(array $spellRow): array
    {
        $params = [
            'effect_direction' => 'action',
        ];
        if ($spellRow['value'] !== null) {
            $params['value'] = $spellRow['value'];
            $params['value_formula'] = $spellRow['value'];
            $params['dice_formula'] = $spellRow['value'];
        }
        if ($spellRow['element'] !== null) {
            $params['characteristic'] = $spellRow['element'];
        }
        if ($spellRow['characteristic'] !== null) {
            $params['characteristic'] = $spellRow['characteristic'];
        }
        if ($spellRow['life_steal'] !== null) {
            $params['life_steal_formula'] = $spellRow['life_steal'];
        }

        return $params;
    }

    /**
     * @param  list<string>  $skipped
     */
    private function attachConditionSubEffect(
        EffectDegree $degree,
        string $conditionName,
        int $order,
        string $monsterName,
        string $spellName,
        array &$skipped
    ): void {
        $subId = SubEffect::query()->where('slug', 'appliquer-etat')->value('id');
        $conditionId = Condition::query()->where('name', $conditionName)->value('id');
        if ($subId === null) {
            $skipped[] = $monsterName.' / '.$spellName.' : sous-effet « appliquer-etat » introuvable';

            return;
        }
        if ($conditionId === null) {
            $skipped[] = $monsterName.' / '.$spellName.' : état « '.$conditionName.' » introuvable';

            return;
        }

        $degree->effectSubEffects()->create([
            'sub_effect_id' => (int) $subId,
            'order' => $order,
            'scope' => Effect::SCOPE_GENERAL,
            'params' => [
                'condition_id' => (int) $conditionId,
                'dispellable' => true,
            ],
            'crit_only' => false,
            'duration_formula' => null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $spellRow
     */
    private function effectText(array $spellRow): string
    {
        $bits = [];
        if ($spellRow['kind'] === 'frapper' && $spellRow['value'] !== null) {
            $label = 'Dégâts '.$spellRow['value'];
            if ($spellRow['life_steal'] !== null) {
                $label .= ', vol de vie '.$spellRow['life_steal'];
            }
            $bits[] = $label.'.';
        } elseif ($spellRow['kind'] === 'soigner' && $spellRow['value'] !== null) {
            $bits[] = 'Soigne '.$spellRow['value'].'.';
        } elseif ($spellRow['kind'] === 'protéger' && $spellRow['value'] !== null) {
            $bits[] = 'Protège ('.$spellRow['value'].').';
        } elseif ($spellRow['kind'] === 'donner-pv-temporaires' && $spellRow['value'] !== null) {
            $bits[] = 'PV temporaires '.$spellRow['value'].'.';
        } elseif ($spellRow['kind'] === 'appliquer-etat' && $spellRow['condition'] !== null) {
            $bits[] = 'Applique '.$spellRow['condition'].'.';
        } elseif ($spellRow['value'] !== null) {
            $bits[] = $spellRow['value'].'.';
        }
        if ($spellRow['kind'] !== 'appliquer-etat' && $spellRow['condition'] !== null) {
            $bits[] = 'Applique '.$spellRow['condition'].'.';
        }
        $bits[] = $spellRow['pa'].' PA.';

        return implode(' ', $bits);
    }

    private function findMonster(string $officialId): ?Monster
    {
        return Monster::query()->where('official_id', $officialId)->with('creature')->first();
    }

    /**
     * @param  array<string, mixed>  $entry
     * @return array<string, mixed>
     */
    private function creatureAttributes(array $entry, Creature $creature): array
    {
        $ca = (string) ($entry['stats']['ca'] ?? '10');

        $stats = [
            'life' => '20',
            'pa' => '4',
            'pm' => '3',
            'po' => '1',
            'ini' => $ca,
            'invocation' => '0',
            'touch' => $ca,
            'ca' => $ca,
            'dodge_pa' => '0',
            'dodge_pm' => '0',
            'fuite' => '0',
            'tacle' => '0',
            'vitality' => '8',
            'sagesse' => '8',
            'strong' => '8',
            'intel' => '8',
            'agi' => '8',
            'chance' => '8',
        ];
        foreach ($entry['stats'] as $key => $value) {
            $stats[$key] = $value;
        }
        if (! isset($entry['stats']['ini'])) {
            $stats['ini'] = $stats['ca'];
        }
        if (! isset($entry['stats']['touch'])) {
            $stats['touch'] = $stats['ca'];
        }

        return array_merge($stats, [
            'name' => $entry['name'],
            'description' => $entry['description'],
            'other_info' => $entry['other_info'],
            'location' => $entry['location'],
            'hostility' => $entry['hostility'],
            'level' => (string) $entry['level'],
            'state' => Creature::STATE_AUTO,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $this->createdById(),
            ...$this->imageAttribute($creature, $entry['image'] ?? null),
        ]);
    }

    /**
     * Pose l'URL DofusDB si la créature n'a pas encore d'image locale.
     *
     * @return array{image?: string}
     */
    private function imageAttribute(Creature $creature, mixed $image): array
    {
        if (! is_string($image)) {
            return [];
        }
        $url = trim($image);
        if ($url === '' || (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://'))) {
            return [];
        }
        $current = is_string($creature->image) ? trim($creature->image) : '';
        if ($current !== '' && ! str_contains($current, 'api.dofusdb.fr')) {
            return [];
        }

        return ['image' => $url];
    }

    /**
     * `creatures.created_by` n’est pas nullable. Utilisateur système, sinon le premier compte.
     */
    private function createdById(): int
    {
        $id = User::getSystemUser()?->id ?? User::query()->orderBy('id')->value('id');
        if ($id !== null) {
            return (int) $id;
        }

        return User::factory()->create()->id;
    }

    /**
     * @param  array<string, mixed>  $entry
     * @return array<string, mixed>
     */
    private function monsterAttributes(array $entry, int $creatureId): array
    {
        $raceId = null;
        if ($entry['monster_race'] !== null) {
            $raceId = MonsterRace::query()->where('name', $entry['monster_race'])->value('id');
        }

        $bossPa = $entry['is_boss'] ? $entry['boss_pa'] : '';

        return [
            'creature_id' => $creatureId,
            'official_id' => $entry['official_id'],
            'dofus_version' => '3',
            'auto_update' => false,
            'size' => $entry['size'],
            'is_boss' => $entry['is_boss'] ? 1 : 0,
            'boss_pa' => $bossPa,
            'monster_race_id' => $raceId,
            'state' => EntityState::Auto->value,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ];
    }

    /**
     * @param  list<string>  $names
     * @param  list<string>  $skipped
     */
    private function syncTraits(Creature $creature, array $names, string $monsterName, array &$skipped): void
    {
        $ids = [];
        foreach ($names as $name) {
            $id = CreatureTrait::query()->where('name', $name)->value('id');
            if ($id === null) {
                $skipped[] = $monsterName.' : trait « '.$name.' » introuvable';

                continue;
            }
            $ids[] = (int) $id;
        }
        $creature->creatureTraits()->sync($ids);
    }

    /**
     * @param  list<string>  $names
     * @param  list<string>  $skipped
     */
    private function syncCapabilities(Creature $creature, array $names, string $monsterName, array &$skipped): void
    {
        $ids = [];
        foreach ($names as $name) {
            $id = Capability::query()->where('name', $name)->value('id');
            if ($id === null) {
                $skipped[] = $monsterName.' : capacité « '.$name.' » introuvable';

                continue;
            }
            $ids[] = (int) $id;
        }
        $creature->capabilities()->sync($ids);
    }
}
