<?php

declare(strict_types=1);

namespace Tests\Feature\Characteristic;

use App\Enums\CharacteristicNature;
use App\Models\Characteristic;
use App\Models\CharacteristicConsumable;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicProfile;
use App\Models\CharacteristicSpell;
use App\Services\Characteristics\CharacteristicNatureQualityService;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\ConsumableCharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\ProfileCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Cohérence des natures et des clés entre crochets dans les formules.
 */
final class CharacteristicNatureAndKeyIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
        $this->seed(ConsumableCharacteristicSeeder::class);
        $this->seed(ProfileCharacteristicSeeder::class);
    }

    public function test_every_characteristic_has_a_valid_nature(): void
    {
        if (! Schema::hasColumn('characteristics', 'nature')) {
            $this->markTestSkipped('Colonne nature absente');
        }

        $this->assertGreaterThan(100, Characteristic::query()->count());

        $invalid = Characteristic::query()
            ->whereNotIn('nature', CharacteristicNature::values())
            ->orWhereNull('nature')
            ->pluck('key')
            ->all();

        $this->assertSame([], $invalid, 'Natures invalides : '.implode(', ', $invalid));
    }

    public function test_nature_coherence_rules(): void
    {
        if (! Schema::hasColumn('characteristics', 'nature')) {
            $this->markTestSkipped('Colonne nature absente');
        }

        $quality = app(CharacteristicNatureQualityService::class);
        $failures = [];
        Characteristic::query()
            ->with('intrinsicCharacteristic')
            ->orderBy('key')
            ->each(function (Characteristic $c) use ($quality, &$failures): void {
                $issues = $quality->issuesFor($c);
                if ($issues !== []) {
                    $failures[] = $c->key.': '.implode('; ', $issues);
                }
            });

        $this->assertSame([], $failures, implode("\n", array_slice($failures, 0, 40)));
    }

    public function test_formula_placeholders_resolve_to_existing_keys(): void
    {
        $knownKeys = Characteristic::query()->pluck('key')->all();
        $known = array_fill_keys($knownKeys, true);
        $reserved = ['level' => true, 'power' => true, 'd' => true, 'base' => true];

        $models = [
            CharacteristicCreature::class,
            CharacteristicObject::class,
            CharacteristicSpell::class,
            CharacteristicConsumable::class,
            CharacteristicProfile::class,
        ];

        $failures = [];
        foreach ($models as $modelClass) {
            if (! class_exists($modelClass) || ! Schema::hasTable((new $modelClass)->getTable())) {
                continue;
            }
            $modelClass::query()->orderBy('id')->each(function ($row) use ($known, $reserved, &$failures): void {
                foreach (['formula', 'conversion_formula', 'norms_formula', 'min', 'max', 'default_value'] as $attr) {
                    $value = $row->getAttribute($attr);
                    if (! is_string($value) || $value === '') {
                        continue;
                    }
                    if (preg_match_all('/\[([a-z0-9_]+)\]/i', $value, $m) < 1) {
                        continue;
                    }
                    foreach ($m[1] as $placeholder) {
                        if (isset($reserved[$placeholder]) || isset($known[$placeholder])) {
                            continue;
                        }
                        // Placeholders courts (db_column) tolérés
                        if (! str_contains($placeholder, '_')) {
                            continue;
                        }
                        $failures[] = ($row->characteristic->key ?? '#'.$row->id).' '.$attr.' → ['.$placeholder.']';
                    }
                }
            });
        }

        $this->assertSame([], $failures, implode("\n", array_slice($failures, 0, 50)));
    }

    public function test_no_mastery_characteristics_in_object_group(): void
    {
        $keys = Characteristic::query()
            ->where('group', 'object')
            ->where('key', 'like', '%mastery%')
            ->pluck('key')
            ->all();

        $this->assertSame([], $keys);
    }

    public function test_profile_and_consumable_groups_exist(): void
    {
        $this->assertTrue(Schema::hasTable('characteristic_profile'));
        $this->assertTrue(Schema::hasTable('characteristic_consumable'));
        $this->assertGreaterThan(0, Characteristic::query()->where('group', 'profile')->count());
        $this->assertGreaterThan(0, Characteristic::query()->where('group', 'consumable')->count());
    }
}
