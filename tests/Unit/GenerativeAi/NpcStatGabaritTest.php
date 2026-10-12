<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Contracts\Characteristic\CharacteristicDefinitionLookup;
use App\Services\Characteristic\Norms\NormsResolver;
use App\Services\GenerativeAi\NpcStatGabarit;
use App\Support\ElementBitmask;
use App\Support\Npc\NpcRole;
use PHPUnit\Framework\TestCase;

final class NpcStatGabaritTest extends TestCase
{
    private NpcStatGabarit $gabarit;

    protected function setUp(): void
    {
        $life = [
            'weak' => array_fill(0, 20, 20),
            'neutral' => array_fill(0, 20, 40),
            'strong' => array_fill(0, 20, 60),
            'very_strong' => array_fill(0, 20, 80),
        ];
        $pa = [
            'weak' => array_fill(0, 20, 5),
            'neutral' => array_fill(0, 20, 6),
            'strong' => array_fill(0, 20, 7),
            'very_strong' => array_fill(0, 20, 8),
        ];
        $pm = ['weak' => array_fill(0, 20, 3), 'neutral' => array_fill(0, 20, 3), 'strong' => array_fill(0, 20, 4), 'very_strong' => array_fill(0, 20, 4)];
        $ca = ['weak' => array_fill(0, 20, 10), 'neutral' => array_fill(0, 20, 12), 'strong' => array_fill(0, 20, 14), 'very_strong' => array_fill(0, 20, 15)];
        $hit = ['weak' => array_fill(0, 20, 1), 'neutral' => array_fill(0, 20, 2), 'strong' => array_fill(0, 20, 3), 'very_strong' => array_fill(0, 20, 4)];
        $mod = ['weak' => array_fill(0, 20, 0), 'neutral' => array_fill(0, 20, 1), 'strong' => array_fill(0, 20, 2), 'very_strong' => array_fill(0, 20, 3)];

        $grids = [
            'life_points_creature' => $life,
            'action_points_creature' => $pa,
            'movement_points_creature' => $pm,
            'armor_class_creature' => $ca,
            'hit_dice_creature' => $hit,
            'modifier_vitality_creature' => $mod,
        ];

        $getter = new class($grids) implements CharacteristicDefinitionLookup
        {
            /** @param  array<string, array<string, list<int>>>  $grids */
            public function __construct(private readonly array $grids) {}

            public function getDefinition(string $key, string $entity): ?array
            {
                return ['norms_grid' => $this->grids[$key] ?? null];
            }
        };

        $this->gabarit = new NpcStatGabarit($getter, new NormsResolver);
    }

    public function test_social_uses_weak_band_norms(): void
    {
        $stats = $this->gabarit->forLevelAndRole(4, NpcRole::SOCIAL);

        $this->assertSame('weak', $stats['band']);
        $this->assertSame('20', $stats['life']);
        $this->assertSame('5', $stats['pa']);
        $this->assertSame('12', $stats['chance']);
    }

    public function test_guard_is_stronger_than_merchant(): void
    {
        $guard = $this->gabarit->forLevelAndRole(5, NpcRole::GUARD);
        $merchant = $this->gabarit->forLevelAndRole(5, NpcRole::MERCHANT);

        $this->assertGreaterThan((int) $merchant['life'], (int) $guard['life']);
        $this->assertSame('strong', $guard['band']);
        $this->assertSame('weak', $merchant['band']);
        $this->assertSame('14', $guard['strong']);
    }

    public function test_validate_stats_rejects_life_outside_band(): void
    {
        $errors = $this->gabarit->validateStats(
            ['life' => '999', 'pa' => '6'],
            4,
            NpcRole::GUARD
        );

        $this->assertNotSame([], $errors);
        $this->assertStringContainsString('PV hors gabarit', $errors[0]);
    }

    public function test_validate_stats_accepts_band_and_pa(): void
    {
        $expected = $this->gabarit->forLevelAndRole(4, NpcRole::GUARD);
        $errors = $this->gabarit->validateStats(
            ['life' => $expected['life'], 'pa' => $expected['pa']],
            4,
            NpcRole::GUARD
        );

        $this->assertSame([], $errors);
    }

    public function test_omitted_primary_follows_spell_element(): void
    {
        $expected = $this->gabarit->forLevelAndRole(4, NpcRole::SOCIAL);
        $this->assertSame('12', $expected['chance']);
        $this->assertSame('8', $expected['strong']);

        $key = $this->gabarit->dominantPrimaryStatKey([
            (object) ['element' => ElementBitmask::fromSlug('earth')],
            (object) ['element' => ElementBitmask::fromSlug('earth')],
        ]);
        $this->assertSame('strong', $key);

        $filled = $this->gabarit->fillOmittedPrimaryFromElement($expected, ['life' => '22'], $key);
        $this->assertSame('12', $filled['strong']);
        $this->assertSame('8', $filled['chance']);

        $kept = $this->gabarit->fillOmittedPrimaryFromElement($expected, ['strong' => '10'], $key);
        $this->assertSame('8', $kept['strong']);
        $this->assertSame('12', $kept['chance']);
    }
}
