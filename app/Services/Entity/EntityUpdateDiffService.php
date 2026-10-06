<?php

declare(strict_types=1);

namespace App\Services\Entity;

use App\Models\Entity\Creature;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Support\Entity\AccessLevelMutationGuard;
use App\Support\EntityModelRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Instantané avant écriture + tableau de champs avant/après + rétablissement.
 *
 * @example
 * $before = $svc->capture($item);
 * // … écriture …
 * $diff = $svc->remember($user, 'items', $item->id, 'ia', $before, $item->fresh());
 */
final class EntityUpdateDiffService
{
    public const TTL_SECONDS = 7200;

    /**
     * @var list<string>
     */
    private const SKIP_ATTRIBUTES = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'password',
        'email_verified_at',
        'laravel_through_key',
    ];

    /**
     * @var array<string, string>
     */
    private const FIELD_LABELS = [
        'name' => 'Nom',
        'description' => 'Description',
        'state' => 'État',
        'effect' => 'Effet',
        'bonus' => 'Bonus',
        'level' => 'Niveau',
        'auto_update' => 'MAJ auto',
        'official_id' => 'Id officiel',
        'dofusdb_id' => 'Id DofusDB',
        'story' => 'Récit',
        'npc_role' => 'Rôle',
        'breed_id' => 'Classe',
        'specialization_id' => 'Spécialisation',
        'rarity' => 'Rareté',
        'price' => 'Prix',
        'recipe' => 'Recette',
        'image' => 'Image',
        'pa' => 'PA',
        'pm' => 'PM',
        'life' => 'Vitalité',
        'ca' => 'CA',
        'strong' => 'Force',
        'intel' => 'Intelligence',
        'agi' => 'Agilité',
        'chance' => 'Chance',
        'vitality' => 'Vitalité (carac)',
        'sagesse' => 'Sagesse',
        'hostility' => 'Hostilité',
        'size' => 'Taille',
        'is_boss' => 'Boss',
        'boss_pa' => 'PA de boss',
        'read_level' => 'Lecture',
        'write_level' => 'Écriture',
        'spells' => 'Sorts',
        'items' => 'Équipements',
    ];

    /**
     * @return array{
     *     attributes: array<string, mixed>,
     *     creature: array<string, mixed>|null,
     *     preview: array{id: int, name: string, state: string, level: string}
     * }
     */
    public function capture(Model $model): array
    {
        $this->loadGraph($model);
        $creature = $this->creatureOf($model);
        $attributes = $this->attributesOf($model);

        return [
            'attributes' => $attributes,
            'creature' => $creature !== null ? $this->captureCreature($creature) : null,
            'preview' => [
                'id' => (int) $model->getKey(),
                'name' => $this->displayName($model, $creature),
                'state' => (string) ($model->getAttribute('state') ?? $creature?->getAttribute('state') ?? ''),
                'level' => (string) ($model->getAttribute('level') ?? $creature?->getAttribute('level') ?? ''),
            ],
        ];
    }

    /**
     * @param  array{
     *     attributes: array<string, mixed>,
     *     creature: array<string, mixed>|null,
     *     preview: array{id: int, name: string, state: string, level: string}
     * }  $before
     * @return array{
     *     snapshot_id: string,
     *     source: string,
     *     entity_type: string,
     *     entity_id: int,
     *     changed_count: int,
     *     fields: list<array{key: string, label: string, before: string, after: string, changed: bool}>,
     *     before: array{preview: array{id: int, name: string, state: string, level: string}},
     *     after: array{preview: array{id: int, name: string, state: string, level: string}}
     * }
     */
    public function remember(
        User $actor,
        string $entityType,
        int $entityId,
        string $source,
        array $before,
        Model $afterModel,
    ): array {
        $after = $this->capture($afterModel);
        $fields = $this->diff($before, $after);
        $snapshotId = (string) Str::uuid();
        $plural = EntityModelRegistry::normalizeType($entityType);

        Cache::put($this->cacheKey((int) $actor->id, $snapshotId), [
            'user_id' => (int) $actor->id,
            'entity_type' => $plural,
            'entity_id' => $entityId,
            'source' => $source,
            'before' => $before,
            'created_spell_ids' => $this->createdIaSpellIds($before, $after),
        ], now()->addSeconds(self::TTL_SECONDS));

        $changedCount = 0;
        foreach ($fields as $row) {
            if ($row['changed']) {
                $changedCount++;
            }
        }

        return [
            'snapshot_id' => $snapshotId,
            'source' => $source,
            'entity_type' => $plural,
            'entity_id' => $entityId,
            'changed_count' => $changedCount,
            'fields' => $fields,
            'before' => ['preview' => $before['preview']],
            'after' => ['preview' => $after['preview']],
        ];
    }

    public function restore(User $actor, string $entityType, int $entityId, string $snapshotId): Model
    {
        $payload = Cache::get($this->cacheKey((int) $actor->id, $snapshotId));
        if (! is_array($payload)) {
            throw new HttpException(422, 'Instantané expiré ou introuvable. La version actuelle est conservée.');
        }

        $plural = EntityModelRegistry::normalizeType($entityType);
        if (($payload['user_id'] ?? null) !== $actor->id
            || ($payload['entity_type'] ?? null) !== $plural
            || (int) ($payload['entity_id'] ?? 0) !== $entityId
        ) {
            throw new HttpException(403, 'Cet instantané ne correspond pas à cette fiche.');
        }

        $entity = EntityModelRegistry::resolveModel($plural, $entityId);
        if (! $entity instanceof Model) {
            throw new HttpException(404, 'Entité introuvable.');
        }

        $before = is_array($payload['before'] ?? null) ? $payload['before'] : [];
        $this->applyAttributes($entity, is_array($before['attributes'] ?? null) ? $before['attributes'] : [], $actor);
        $entity->save();

        $creatureSnap = is_array($before['creature'] ?? null) ? $before['creature'] : null;
        if ($creatureSnap !== null) {
            $this->restoreCreature($entity, $creatureSnap, $actor);
        }

        $createdSpellIds = $payload['created_spell_ids'] ?? [];
        if (is_array($createdSpellIds) && $createdSpellIds !== []) {
            Spell::query()
                ->whereIn('id', array_map('intval', $createdSpellIds))
                ->where('official_id', 'like', 'ia:encounter:%')
                ->get()
                ->each(static function (Spell $spell): void {
                    $spell->delete();
                });
        }

        Cache::forget($this->cacheKey((int) $actor->id, $snapshotId));

        return $entity->fresh() ?? $entity;
    }

    /**
     * Conserve la nouvelle version sauf les clés listées, rétablies depuis l’instantané.
     *
     * @param  list<string>  $restoreKeys
     */
    public function apply(User $actor, string $entityType, int $entityId, string $snapshotId, array $restoreKeys): Model
    {
        $payload = Cache::get($this->cacheKey((int) $actor->id, $snapshotId));
        if (! is_array($payload)) {
            throw new HttpException(422, 'Instantané expiré ou introuvable. La version actuelle est conservée.');
        }

        $plural = EntityModelRegistry::normalizeType($entityType);
        if (($payload['user_id'] ?? null) !== $actor->id
            || ($payload['entity_type'] ?? null) !== $plural
            || (int) ($payload['entity_id'] ?? 0) !== $entityId
        ) {
            throw new HttpException(403, 'Cet instantané ne correspond pas à cette fiche.');
        }

        $entity = EntityModelRegistry::resolveModel($plural, $entityId);
        if (! $entity instanceof Model) {
            throw new HttpException(404, 'Entité introuvable.');
        }

        $keys = [];
        foreach ($restoreKeys as $key) {
            if (is_string($key) && trim($key) !== '') {
                $keys[] = trim($key);
            }
        }
        $keys = array_values(array_unique($keys));

        if ($keys === []) {
            Cache::forget($this->cacheKey((int) $actor->id, $snapshotId));

            return $entity->fresh() ?? $entity;
        }

        $before = is_array($payload['before'] ?? null) ? $payload['before'] : [];
        $beforeAttrs = is_array($before['attributes'] ?? null) ? $before['attributes'] : [];
        $attrSubset = [];
        $creatureKeys = [];
        $restoreSpells = false;
        $restoreItems = false;
        foreach ($keys as $key) {
            if ($key === 'spells') {
                $restoreSpells = true;

                continue;
            }
            if ($key === 'items') {
                $restoreItems = true;

                continue;
            }
            if (str_starts_with($key, 'creature.')) {
                $creatureKeys[] = substr($key, 9);

                continue;
            }
            if (array_key_exists($key, $beforeAttrs)) {
                $attrSubset[$key] = $beforeAttrs[$key];
            }
        }

        if ($attrSubset !== []) {
            $this->applyAttributes($entity, $attrSubset, $actor);
            $entity->save();
        }

        $creatureSnap = is_array($before['creature'] ?? null) ? $before['creature'] : null;
        if ($creatureSnap !== null && ($creatureKeys !== [] || $restoreSpells || $restoreItems)) {
            $this->restoreCreaturePartial($entity, $creatureSnap, $creatureKeys, $restoreSpells, $restoreItems, $actor);
        }

        if ($restoreSpells) {
            $createdSpellIds = $payload['created_spell_ids'] ?? [];
            if (is_array($createdSpellIds) && $createdSpellIds !== []) {
                Spell::query()
                    ->whereIn('id', array_map('intval', $createdSpellIds))
                    ->where('official_id', 'like', 'ia:encounter:%')
                    ->get()
                    ->each(static function (Spell $spell): void {
                        $spell->delete();
                    });
            }
        }

        Cache::forget($this->cacheKey((int) $actor->id, $snapshotId));

        return $entity->fresh() ?? $entity;
    }

    /**
     * @param  array{
     *     attributes: array<string, mixed>,
     *     creature: array<string, mixed>|null,
     *     preview: array{id: int, name: string, state: string, level: string}
     * }  $before
     * @param  array{
     *     attributes: array<string, mixed>,
     *     creature: array<string, mixed>|null,
     *     preview: array{id: int, name: string, state: string, level: string}
     * }  $after
     * @return list<array{key: string, label: string, before: string, after: string, changed: bool}>
     */
    public function diff(array $before, array $after): array
    {
        $rows = [];
        $beforeAttrs = is_array($before['attributes'] ?? null) ? $before['attributes'] : [];
        $afterAttrs = is_array($after['attributes'] ?? null) ? $after['attributes'] : [];
        $keys = array_values(array_unique([...array_keys($beforeAttrs), ...array_keys($afterAttrs)]));
        sort($keys);
        foreach ($keys as $key) {
            $rows[] = $this->fieldRow(
                $key,
                $this->label($key),
                $beforeAttrs[$key] ?? null,
                $afterAttrs[$key] ?? null,
            );
        }

        $beforeCreature = is_array($before['creature'] ?? null) ? $before['creature'] : [];
        $afterCreature = is_array($after['creature'] ?? null) ? $after['creature'] : [];
        $beforeCreatureAttrs = is_array($beforeCreature['attributes'] ?? null) ? $beforeCreature['attributes'] : [];
        $afterCreatureAttrs = is_array($afterCreature['attributes'] ?? null) ? $afterCreature['attributes'] : [];
        $creatureKeys = array_values(array_unique([...array_keys($beforeCreatureAttrs), ...array_keys($afterCreatureAttrs)]));
        sort($creatureKeys);
        foreach ($creatureKeys as $key) {
            $rows[] = $this->fieldRow(
                'creature.'.$key,
                'Créature · '.$this->label($key),
                $beforeCreatureAttrs[$key] ?? null,
                $afterCreatureAttrs[$key] ?? null,
            );
        }

        if ($beforeCreature !== [] || $afterCreature !== []) {
            $rows[] = $this->fieldRow(
                'spells',
                $this->label('spells'),
                $beforeCreature['spells'] ?? [],
                $afterCreature['spells'] ?? [],
            );
            $rows[] = $this->fieldRow(
                'items',
                $this->label('items'),
                $beforeCreature['items'] ?? [],
                $afterCreature['items'] ?? [],
            );
        }

        usort($rows, static function (array $a, array $b): int {
            if ($a['changed'] === $b['changed']) {
                return strcmp($a['label'], $b['label']);
            }

            return $a['changed'] ? -1 : 1;
        });

        return $rows;
    }

    private function cacheKey(int $userId, string $snapshotId): string
    {
        return 'entity-update-diff:'.$userId.':'.$snapshotId;
    }

    /**
     * @return array<string, mixed>
     */
    private function attributesOf(Model $model): array
    {
        $out = [];
        foreach ($model->getAttributes() as $key => $value) {
            if (in_array($key, self::SKIP_ATTRIBUTES, true)) {
                continue;
            }
            $out[$key] = $value;
        }
        ksort($out);

        return $out;
    }

    /**
     * @return array{
     *     id: int,
     *     attributes: array<string, mixed>,
     *     spell_ids: list<int>,
     *     item_ids: array<int, int>,
     *     spells: list<array{id: int, name: string, effect: string}>,
     *     items: list<array{id: int, name: string, quantity: int}>
     * }
     */
    private function captureCreature(Creature $creature): array
    {
        $creature->loadMissing(['spells', 'items']);
        $spells = [];
        $spellIds = [];
        foreach ($creature->spells as $spell) {
            $id = (int) $spell->id;
            $spellIds[] = $id;
            $spells[] = [
                'id' => $id,
                'name' => (string) $spell->name,
                'effect' => (string) ($spell->effect ?? ''),
            ];
        }

        $items = [];
        $itemIds = [];
        foreach ($creature->items as $item) {
            $id = (int) $item->id;
            $qty = (int) ($item->pivot->quantity ?? 1);
            $itemIds[$id] = $qty;
            $items[] = [
                'id' => $id,
                'name' => (string) $item->name,
                'quantity' => $qty,
            ];
        }

        return [
            'id' => (int) $creature->id,
            'attributes' => $this->attributesOf($creature),
            'spell_ids' => $spellIds,
            'item_ids' => $itemIds,
            'spells' => $spells,
            'items' => $items,
        ];
    }

    /**
     * Applique un sac d’attributs. `read_level` / `write_level` restent
     * réservés aux admins (SEC-07), même via rétablissement d’instantané.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function applyAttributes(Model $model, array $attributes, User $actor): void
    {
        $attributes = AccessLevelMutationGuard::stripUnlessAdmin($actor, $attributes);
        foreach ($attributes as $key => $value) {
            if (! is_string($key) || in_array($key, self::SKIP_ATTRIBUTES, true)) {
                continue;
            }
            $model->setAttribute($key, $value);
        }
    }

    /**
     * @param  array<string, mixed>  $creatureSnap
     */
    private function restoreCreature(Model $entity, array $creatureSnap, User $actor): void
    {
        $creatureId = (int) ($creatureSnap['id'] ?? 0);
        $creature = $this->creatureOf($entity);
        if ($creature === null && $creatureId > 0) {
            $creature = Creature::query()->find($creatureId);
        }
        if (! $creature instanceof Creature) {
            return;
        }

        $this->applyAttributes($creature, is_array($creatureSnap['attributes'] ?? null) ? $creatureSnap['attributes'] : [], $actor);
        $creature->save();

        $spellIds = array_values(array_filter(
            array_map('intval', is_array($creatureSnap['spell_ids'] ?? null) ? $creatureSnap['spell_ids'] : []),
            static fn (int $id): bool => $id > 0
        ));
        $creature->spells()->sync($spellIds);

        $itemIds = is_array($creatureSnap['item_ids'] ?? null) ? $creatureSnap['item_ids'] : [];
        $sync = [];
        foreach ($itemIds as $id => $qty) {
            $itemId = (int) $id;
            if ($itemId < 1) {
                continue;
            }
            $sync[$itemId] = ['quantity' => max(1, (int) $qty)];
        }
        if (method_exists($creature, 'items')) {
            $creature->items()->sync($sync);
        }
    }

    /**
     * @param  array<string, mixed>  $creatureSnap
     * @param  list<string>  $attributeKeys
     */
    private function restoreCreaturePartial(
        Model $entity,
        array $creatureSnap,
        array $attributeKeys,
        bool $restoreSpells,
        bool $restoreItems,
        User $actor,
    ): void {
        $creatureId = (int) ($creatureSnap['id'] ?? 0);
        $creature = $this->creatureOf($entity);
        if ($creature === null && $creatureId > 0) {
            $creature = Creature::query()->find($creatureId);
        }
        if (! $creature instanceof Creature) {
            return;
        }

        if ($attributeKeys !== []) {
            $source = is_array($creatureSnap['attributes'] ?? null) ? $creatureSnap['attributes'] : [];
            $subset = [];
            foreach ($attributeKeys as $key) {
                if (array_key_exists($key, $source)) {
                    $subset[$key] = $source[$key];
                }
            }
            if ($subset !== []) {
                $this->applyAttributes($creature, $subset, $actor);
                $creature->save();
            }
        }

        if ($restoreSpells) {
            $spellIds = array_values(array_filter(
                array_map('intval', is_array($creatureSnap['spell_ids'] ?? null) ? $creatureSnap['spell_ids'] : []),
                static fn (int $id): bool => $id > 0
            ));
            $creature->spells()->sync($spellIds);
        }

        if ($restoreItems && method_exists($creature, 'items')) {
            $itemIds = is_array($creatureSnap['item_ids'] ?? null) ? $creatureSnap['item_ids'] : [];
            $sync = [];
            foreach ($itemIds as $id => $qty) {
                $itemId = (int) $id;
                if ($itemId < 1) {
                    continue;
                }
                $sync[$itemId] = ['quantity' => max(1, (int) $qty)];
            }
            $creature->items()->sync($sync);
        }
    }

    /**
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @return list<int>
     */
    private function createdIaSpellIds(array $before, array $after): array
    {
        $beforeIds = is_array($before['creature']['spell_ids'] ?? null) ? $before['creature']['spell_ids'] : [];
        $afterIds = is_array($after['creature']['spell_ids'] ?? null) ? $after['creature']['spell_ids'] : [];
        $created = array_values(array_diff(
            array_map('intval', $afterIds),
            array_map('intval', $beforeIds),
        ));
        if ($created === []) {
            return [];
        }

        return Spell::query()
            ->whereIn('id', $created)
            ->where('official_id', 'like', 'ia:encounter:%')
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @return array{key: string, label: string, before: string, after: string, changed: bool}
     */
    private function fieldRow(string $key, string $label, mixed $before, mixed $after): array
    {
        $normalizedBefore = $this->normalize($before);
        $normalizedAfter = $this->normalize($after);

        return [
            'key' => $key,
            'label' => $label,
            'before' => $this->display($before),
            'after' => $this->display($after),
            'changed' => $normalizedBefore !== $normalizedAfter,
        ];
    }

    private function label(string $key): string
    {
        return self::FIELD_LABELS[$key] ?? str_replace('_', ' ', $key);
    }

    private function display(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }
        if (is_bool($value)) {
            return $value ? 'oui' : 'non';
        }
        if (is_array($value)) {
            if ($value === []) {
                return '—';
            }
            if (array_is_list($value) && isset($value[0]) && is_array($value[0]) && array_key_exists('name', $value[0])) {
                $parts = [];
                foreach ($value as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $name = (string) ($row['name'] ?? '');
                    $extra = (string) ($row['effect'] ?? $row['quantity'] ?? '');
                    $parts[] = $extra !== '' && $extra !== '1' ? $name.' ('.$extra.')' : $name;
                }

                return $parts === [] ? '—' : implode("\n", $parts);
            }

            $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            return is_string($json) ? $json : '—';
        }

        return (string) $value;
    }

    private function normalize(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    private function loadGraph(Model $model): void
    {
        if (method_exists($model, 'creature')) {
            $relation = $model->creature();
            if ($relation instanceof Relation) {
                $model->loadMissing(['creature.spells', 'creature.items']);
            }
        }
    }

    private function creatureOf(Model $model): ?Creature
    {
        if (! method_exists($model, 'creature')) {
            return null;
        }
        $creature = $model->getRelationValue('creature') ?? $model->creature()->first();

        return $creature instanceof Creature ? $creature : null;
    }

    private function displayName(Model $model, ?Creature $creature): string
    {
        $name = $model->getAttribute('name') ?? $creature?->getAttribute('name') ?? '';

        return is_string($name) && $name !== '' ? $name : 'Fiche #'.$model->getKey();
    }
}
