<?php

declare(strict_types=1);

use App\Models\Characteristic;
use App\Models\Entity\Consumable;
use App\Models\ObjectEffect;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Remappe les ObjectEffect de consommables encore liés à des caractéristiques groupe « object »
 * vers leurs jumelles groupe « consumable ».
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('object_effects') || ! Schema::hasTable('characteristics')) {
            return;
        }

        $explicit = [
            'life_points_restore_object' => 'soin_consumable',
            'shield_points_object' => 'bouclier_consumable',
            'temporary_life_points_object' => 'pv_temporaires_consumable',
        ];

        $objectKeys = Characteristic::query()->where('group', 'object')->pluck('id', 'key');
        $consumableKeys = Characteristic::query()->where('group', 'consumable')->pluck('id', 'key');

        $map = $explicit;
        foreach ($objectKeys as $objectKey => $objectId) {
            if (isset($map[$objectKey])) {
                continue;
            }
            if (! str_ends_with((string) $objectKey, '_object')) {
                continue;
            }
            $stem = substr((string) $objectKey, 0, -7);
            $twinKey = $stem.'_consumable';
            if ($consumableKeys->has($twinKey)) {
                $map[$objectKey] = $twinKey;
            }
        }

        $keyToId = [];
        foreach ($map as $fromKey => $toKey) {
            if ($objectKeys->has($fromKey) && $consumableKeys->has($toKey)) {
                $keyToId[(int) $objectKeys[$fromKey]] = (int) $consumableKeys[$toKey];
            }
        }

        if ($keyToId === []) {
            $this->writeReport(['updated' => 0, 'rows' => []]);

            return;
        }

        $rows = [];
        $updated = 0;

        ObjectEffect::query()
            ->where('object_effectable_type', Consumable::class)
            ->whereIn('characteristic_id', array_keys($keyToId))
            ->orderBy('id')
            ->chunkById(200, function ($effects) use ($keyToId, &$rows, &$updated): void {
                foreach ($effects as $effect) {
                    $fromId = (int) $effect->characteristic_id;
                    $toId = $keyToId[$fromId] ?? null;
                    if ($toId === null) {
                        continue;
                    }
                    $fromKey = Characteristic::query()->whereKey($fromId)->value('key');
                    $toKey = Characteristic::query()->whereKey($toId)->value('key');
                    $effect->update(['characteristic_id' => $toId]);
                    $updated++;
                    $rows[] = [
                        'object_effect_id' => (int) $effect->id,
                        'consumable_id' => (int) $effect->object_effectable_id,
                        'from_characteristic_id' => $fromId,
                        'to_characteristic_id' => $toId,
                        'from_key' => $fromKey,
                        'to_key' => $toKey,
                    ];
                }
            });

        $this->writeReport([
            'updated' => $updated,
            'rows' => $rows,
        ]);
    }

    /**
     * @param  array{updated: int, rows: list<array<string, mixed>>}  $payload
     */
    private function writeReport(array $payload): void
    {
        $dir = storage_path('app/reports');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        $path = $dir.'/remap-consumable-object-effects-'.now()->format('Y-m-d_His').'.json';
        File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function down(): void
    {
        // Non réversible sans le rapport de migration.
    }
};
