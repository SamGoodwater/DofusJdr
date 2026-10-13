<?php

declare(strict_types=1);

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Support\Characteristics\CharacteristicDefinitionNaming;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Applique en base les formules créature complètes (équipement + consommable + sort),
 * les limites absolues (mods, compétences, sauvegardes, bonus de maîtrise) et les formules affichées.
 * Seuls formula, formula_display, min, max et limit_absolute sont réécrits depuis les JSON.
 */
return new class extends Migration
{
    /** @var list<string> */
    private const SYNCED_FIELDS = ['formula', 'formula_display', 'min', 'max', 'limit_absolute'];

    public function up(): void
    {
        if (! Schema::hasColumn('characteristic_creature', 'limit_absolute')) {
            return;
        }

        $root = base_path(CharacteristicDefinitionNaming::RELATIVE_ROOT);
        $ids = Characteristic::query()->pluck('id', 'key');

        foreach (glob($root.'/creature/*-definition.json') ?: [] as $path) {
            $data = json_decode((string) file_get_contents($path), true);
            $key = $data['characteristic']['key'] ?? null;
            $characteristicId = is_string($key) ? ($ids[$key] ?? null) : null;
            if ($characteristicId === null || ! is_array($data['entities'] ?? null)) {
                continue;
            }
            foreach ($data['entities'] as $entity => $row) {
                if (! is_string($entity) || ! is_array($row)) {
                    continue;
                }
                $attributes = [];
                foreach (self::SYNCED_FIELDS as $field) {
                    $attributes[$field] = $row[$field] ?? null;
                }
                CharacteristicCreature::query()->updateOrCreate(
                    ['characteristic_id' => $characteristicId, 'entity' => $entity],
                    $attributes
                );
            }
        }

        CharacteristicObject::query()
            ->whereIn('formula_display', ['level_object', 'level'])
            ->update(['formula_display' => null]);

        app(CharacteristicGetterService::class)->clearCache();
        Cache::forget('characteristics:frontend:v6');
        Cache::forget('characteristics:frontend:v7');
    }

    public function down(): void
    {
        // Données : pas de retour arrière automatique (réappliquer les JSON précédents si besoin).
    }
};
