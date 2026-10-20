<?php

declare(strict_types=1);

namespace Tests\Unit\Type;

use App\Models\Type\SpellType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Models\Type\SpellType::findByLabel
 */
class SpellTypeFindByLabelTest extends TestCase
{
    use RefreshDatabase;

    public function test_finds_by_orientation_name(): void
    {
        SpellType::factory()->create(['name' => 'Amélioration']);

        $found = SpellType::findByLabel('Amélioration');

        $this->assertNotNull($found);
        $this->assertSame('Amélioration', $found->name);
    }

    public function test_resolves_legacy_buff_alias(): void
    {
        SpellType::factory()->create(['name' => 'Amélioration']);

        $found = SpellType::findByLabel('Buff');

        $this->assertNotNull($found);
        $this->assertSame('Amélioration', $found->name);
    }
}
