<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Condition;

use App\Models\Entity\Condition;
use App\Services\Condition\ConditionCanonicalMapper;
use Tests\TestCase;

/**
 * Résolution d’un jeton Dofus vers un état JDR de base.
 */
final class ConditionCanonicalMapperTest extends TestCase
{
    public function test_playable_source_resolves_to_itself(): void
    {
        $playable = $this->playable('Pesanteur');
        $mapper = new ConditionCanonicalMapper;

        $this->assertSame($playable->id, $mapper->resolve($playable)?->id);
    }

    public function test_lourd_without_flags_is_not_pesanteur(): void
    {
        $this->playable('Pesanteur');
        $raw = $this->raw('Lourd', ['dofusdb_id' => 68]);
        $mapper = new ConditionCanonicalMapper;

        $this->assertNull($mapper->resolve($raw));
    }

    public function test_playable_lourd_wins_over_movement_flags(): void
    {
        $this->playable('Pesanteur');
        $lourd = $this->playable('Lourd');
        $raw = $this->raw('Lourd', [
            'dofusdb_id' => 63,
            'cant_be_moved' => true,
            'cant_be_pushed' => true,
        ]);
        $mapper = new ConditionCanonicalMapper;

        $this->assertSame($lourd->id, $mapper->resolve($raw)?->id);
    }

    public function test_movement_flags_map_to_pesanteur(): void
    {
        $playable = $this->playable('Pesanteur');
        $raw = $this->raw('Kabombz', [
            'dofusdb_id' => 9001,
            'cant_be_moved' => true,
        ]);
        $mapper = new ConditionCanonicalMapper;

        $this->assertSame($playable->id, $mapper->resolve($raw)?->id);
    }

    public function test_spell_cast_flag_maps_to_etourdi(): void
    {
        $playable = $this->playable('Étourdi');
        $raw = $this->raw('Saoul', [
            'dofusdb_id' => 9002,
            'prevents_spell_cast' => true,
        ]);
        $mapper = new ConditionCanonicalMapper;

        $this->assertSame($playable->id, $mapper->resolve($raw)?->id);
    }

    public function test_unknown_token_returns_null(): void
    {
        $this->playable('Pesanteur');
        $raw = $this->raw('Invisible', ['dofusdb_id' => 250, 'invulnerable' => true]);
        $mapper = new ConditionCanonicalMapper;

        $this->assertNull($mapper->resolve($raw));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function playable(string $name, array $overrides = []): Condition
    {
        return Condition::query()->create(array_merge([
            'name' => $name,
            'state' => Condition::STATE_PLAYABLE,
            'read_level' => 0,
            'write_level' => 4,
        ], $overrides));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function raw(string $name, array $overrides = []): Condition
    {
        return Condition::query()->create(array_merge([
            'name' => $name,
            'state' => Condition::STATE_RAW,
            'read_level' => 0,
            'write_level' => 4,
        ], $overrides));
    }
}
