<?php

declare(strict_types=1);

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

/**
 * Aligne la réserve Wakfu sur le plafond runtime historique (1–9 via min/max).
 */
return new class extends Migration
{
    public function up(): void
    {
        $characteristic = Characteristic::query()->where('key', 'wakfu_reserve_creature')->first();
        if ($characteristic === null) {
            return;
        }

        CharacteristicCreature::query()
            ->where('characteristic_id', $characteristic->id)
            ->where('entity', '*')
            ->update(['min' => '1', 'max' => '9']);

        app(CharacteristicGetterService::class)->clearCache();
        Cache::forget('characteristics:frontend:v7');
        Cache::forget('characteristics:frontend:v6');
    }

    public function down(): void
    {
        $characteristic = Characteristic::query()->where('key', 'wakfu_reserve_creature')->first();
        if ($characteristic === null) {
            return;
        }

        CharacteristicCreature::query()
            ->where('characteristic_id', $characteristic->id)
            ->where('entity', '*')
            ->update(['min' => '-9', 'max' => '9']);

        app(CharacteristicGetterService::class)->clearCache();
        Cache::forget('characteristics:frontend:v7');
        Cache::forget('characteristics:frontend:v6');
    }
};
