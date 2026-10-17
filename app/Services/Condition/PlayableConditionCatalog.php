<?php

declare(strict_types=1);

namespace App\Services\Condition;

use App\Models\Entity\Condition;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Pose le catalogue d’états jouables (D&D et Dofus retenus) sans réécrire une fiche déjà jouable.
 *
 * @example
 * $stats = app(PlayableConditionCatalog::class)->sync(deleteRaw: true);
 */
final class PlayableConditionCatalog
{
    public function __construct(private readonly ConditionSpellCanonicalRemapper $remapper) {}

    /**
     * @return array{created: int, updated: int, skipped: int, deleted: int}
     */
    public function sync(bool $deleteRaw = false): array
    {
        $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'deleted' => 0];
        $index = $this->indexByNormalizedName();

        foreach ($this->definitions() as $definition) {
            $pool = $this->candidates($index, $definition);
            $playable = $pool->first(fn (Condition $row): bool => $row->state === Condition::STATE_PLAYABLE);
            if ($playable instanceof Condition) {
                $stats['skipped']++;

                continue;
            }

            $payload = $this->payload($definition);
            $chosen = $pool->sortBy(fn (Condition $row): string => sprintf('%d-%08d', $row->image ? 0 : 1, $row->id))->first();
            if ($chosen instanceof Condition) {
                $chosen->update($payload);
                $stats['updated']++;
                $index[ConditionCanonicalMapper::normalizeName($payload['name'])][] = $chosen->fresh() ?? $chosen;

                continue;
            }

            $created = Condition::query()->create($payload);
            $stats['created']++;
            $index[ConditionCanonicalMapper::normalizeName($created->name)][] = $created;
        }

        if ($deleteRaw) {
            $this->remapper->remapAll();
            $stats['deleted'] = $this->deleteRaw();
        }

        return $stats;
    }

    /**
     * @return list<array{name: string, description: string, image: string|null, aliases?: list<string>, flags?: list<string>}>
     */
    public function definitions(): array
    {
        /** @var list<array{name: string, description: string, image: string|null, aliases?: list<string>, flags?: list<string>}> $definitions */
        $definitions = require database_path('seeders/data/entities/conditions/playable-catalog.php');

        return $definitions;
    }

    /**
     * Supprime les états encore en Brut, après avoir détaché la clé canonique.
     */
    public function deleteRaw(): int
    {
        $rawIds = DB::table('conditions')->where('state', Condition::STATE_RAW)->pluck('id');
        if ($rawIds->isEmpty()) {
            return 0;
        }

        DB::table('conditions')->where('state', Condition::STATE_RAW)->update(['canonical_condition_id' => null]);
        foreach ($rawIds->chunk(500) as $chunk) {
            DB::table('conditions')->whereIn('canonical_condition_id', $chunk->all())->update(['canonical_condition_id' => null]);
            DB::table('media')
                ->where('model_type', Condition::class)
                ->whereIn('model_id', $chunk->all())
                ->delete();
        }

        $deleted = 0;
        foreach ($rawIds->chunk(500) as $chunk) {
            $deleted += DB::table('conditions')->whereIn('id', $chunk->all())->delete();
        }

        return $deleted;
    }

    /**
     * @param  array{name: string, description: string, image: string|null, aliases?: list<string>, flags?: list<string>}  $definition
     * @return array<string, mixed>
     */
    private function payload(array $definition): array
    {
        $flags = array_fill_keys(array_keys(Condition::MECHANICAL_FLAG_LABELS), false);
        foreach ($definition['flags'] ?? [] as $flag) {
            if (array_key_exists($flag, $flags)) {
                $flags[$flag] = true;
            }
        }

        return array_merge($flags, [
            'name' => $definition['name'],
            'description' => $definition['description'],
            'state' => Condition::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'image' => $definition['image'],
            'icon' => null,
            'dissipable' => true,
            'dofusdb_id' => null,
            'canonical_condition_id' => null,
            'display_turn_remaining' => false,
            'is_main_state' => false,
            'raw' => null,
        ]);
    }

    /**
     * @return array<string, list<Condition>>
     */
    private function indexByNormalizedName(): array
    {
        $index = [];
        foreach (Condition::query()->get() as $condition) {
            $index[ConditionCanonicalMapper::normalizeName((string) $condition->name)][] = $condition;
        }

        return $index;
    }

    /**
     * @param  array<string, list<Condition>>  $index
     * @param  array{name: string, aliases?: list<string>}  $definition
     * @return Collection<int, Condition>
     */
    private function candidates(array $index, array $definition): Collection
    {
        $pool = collect();
        $labels = array_merge([$definition['name']], $definition['aliases'] ?? []);
        foreach ($labels as $label) {
            foreach ($index[ConditionCanonicalMapper::normalizeName($label)] ?? [] as $row) {
                $pool->put($row->id, $row);
            }
        }

        return $pool;
    }
}
