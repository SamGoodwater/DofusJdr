<?php

declare(strict_types=1);

namespace App\Services\Seeder\Monster;

use App\Enums\EntityState;
use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Models\Type\MonsterRace;
use App\Models\Type\SpellType;
use App\Models\User;
use App\Support\ElementBitmask;

/**
 * Importe les invocations de classe en monstres `auto` + 1 sort-créature.
 *
 * Idempotent. Upsert sur `official_id` `jdr:summon:…`. `auto_update = false`.
 *
 * @example $result = app(ClassSummonSeederImporter::class)->import();
 */
final class ClassSummonSeederImporter
{
    /**
     * @param  bool  $overwrite  False = ignorer les fiches déjà présentes
     * @return array{
     *     created: list<string>,
     *     updated: list<string>,
     *     skipped: list<string>
     * }
     */
    public function import(?ClassSummonCatalog $catalog = null, bool $overwrite = true): array
    {
        $catalog ??= ClassSummonCatalog::load();
        $created = [];
        $updated = [];
        $skipped = [];

        foreach ($catalog->entries() as $entry) {
            $monster = $this->findMonster($entry['official_id']);
            $wasNew = $monster === null;
            $label = $entry['breed'] !== '' ? $entry['breed'].' / '.$entry['name'] : $entry['name'];
            if (! $wasNew && ! $overwrite) {
                $skipped[] = $label.' : déjà présent, conservation';

                continue;
            }

            $spell = $this->syncActionSpell($entry, $skipped);
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

            if ($spell !== null) {
                $creature->spells()->sync([$spell->id]);
            }

            $wasNew ? $created[] = $label : $updated[] = $label;
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    /**
     * @param  array<string, mixed>  $entry
     * @param  list<string>  $skipped
     */
    private function syncActionSpell(array $entry, array &$skipped): ?Spell
    {
        $action = $entry['action'];
        $spell = Spell::query()->where('official_id', $entry['action_official_id'])->first() ?? new Spell;

        $elementSlug = $action['element'];
        $element = is_string($elementSlug) ? ElementBitmask::fromSlug($elementSlug) : null;

        $isHeal = $action['kind'] === 'soigner';
        $typeName = $isHeal ? 'Soin' : 'Dégâts';
        $effectText = $isHeal
            ? 'Soigne '.$action['value'].'. 3 PA.'
            : 'Dégâts '.$action['value'].'. 3 PA.';

        $spell->fill([
            'name' => $action['name'],
            'description' => 'Action de l’invocation « '.$entry['name'].' ».',
            'effect' => $effectText,
            'level' => (string) $entry['character_level'],
            'pa' => '3',
            'po_min' => '1',
            'po_max' => '1',
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
            'is_magic' => $action['is_magic'],
            'powerful' => 0,
            'resolution_mode' => $action['resolution_mode'],
            'attack_characteristic_key' => $isHeal ? null : $action['attack_characteristic_key'],
            'save_characteristic_key' => null,
            'save_dc_formula' => null,
            'save_success_note' => null,
            'auto_success_if_willing_target' => $isHeal,
            'allows_reaction' => false,
            'duration' => null,
            'official_id' => $entry['action_official_id'],
            'state' => Spell::STATE_AUTO,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'auto_update' => false,
        ]);
        $spell->save();

        $type = SpellType::findByLabel($typeName);
        if ($type === null) {
            $skipped[] = $entry['name'].' : type « '.$typeName.' » introuvable pour l’action';
        } else {
            $spell->spellTypes()->sync([$type->id]);
        }

        $subId = SubEffect::query()->where('slug', $action['kind'])->value('id');
        if ($subId === null) {
            $skipped[] = $entry['name'].' : sous-effet « '.$action['kind'].' » introuvable';

            return $spell;
        }

        $slug = 'jdr-summon-'.$entry['key'].'-action';
        if (strlen($slug) > 64) {
            $slug = substr($slug, 0, 64);
        }

        $effect = Effect::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $action['name'],
                'description' => $effectText,
                'target_type' => Effect::TARGET_DIRECT,
            ]
        );
        $effect->fill([
            'name' => $action['name'],
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
                'required_creature_level' => $entry['character_level'],
                'area' => 'point',
                'slug' => $slug.'-d1',
            ]
        );
        $degree->update([
            'required_creature_level' => $entry['character_level'],
            'area' => 'point',
        ]);
        $degree->effectSubEffects()->delete();

        $params = [
            'value' => $action['value'],
            'value_formula' => $action['value'],
            'dice_formula' => $action['value'],
            'effect_direction' => 'action',
        ];
        if ($action['element'] !== null) {
            $params['characteristic'] = $action['element'];
        }

        $degree->effectSubEffects()->create([
            'sub_effect_id' => (int) $subId,
            'order' => 0,
            'scope' => Effect::SCOPE_GENERAL,
            'params' => $params,
            'crit_only' => false,
            'duration_formula' => null,
        ]);

        $spell->effects()->sync([$effect->id]);

        return $spell;
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
        $stats = $this->statsForTier($entry['character_level'], $entry['primary']);

        return array_merge($stats, [
            'name' => $entry['name'],
            'description' => $entry['description'],
            'other_info' => $entry['other_info'],
            'hostility' => 0,
            'level' => (string) $entry['character_level'],
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

        return [
            'creature_id' => $creatureId,
            'official_id' => $entry['official_id'],
            'dofus_version' => '3',
            'auto_update' => false,
            'size' => $entry['size'],
            'is_boss' => 0,
            'boss_pa' => '',
            'monster_race_id' => $raceId,
            'state' => EntityState::Auto->value,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function statsForTier(int $characterLevel, string $primaryKey): array
    {
        if ($characterLevel <= 3) {
            $life = '8';
            $ca = '10';
            $primary = '12';
        } elseif ($characterLevel <= 10) {
            $life = '12';
            $ca = '11';
            $primary = '14';
        } else {
            $life = '16';
            $ca = '12';
            $primary = '16';
        }

        $stats = [
            'life' => $life,
            'pa' => '3',
            'pm' => '1',
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
        $stats[$primaryKey] = $primary;

        return $stats;
    }
}
