<?php

declare(strict_types=1);

namespace App\Support\Entity;

use App\Enums\CharacteristicNature;
use App\Http\Resources\ObjectEffectResource;
use App\Models\Characteristic;
use App\Models\Entity\Consumable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Listes pour les sélecteurs d’effets d’objet (pages d’édition Inertia).
 *
 * Les monstres ne sont plus embarqués (EntityPicker / api.tables) — évite ~2k lignes.
 */
final class ObjectEffectEditOptions
{
    /**
     * @return array{objectEffectCharacteristics: Collection<int, Characteristic>, objectEffectMonsters: array<int, never>}
     */
    public static function toArray(?Model $parent = null): array
    {
        $query = Characteristic::query()->orderBy('name');

        if ($parent instanceof Consumable) {
            $query->where('group', 'consumable')
                ->whereIn('nature', [
                    CharacteristicNature::BuffDebuff->value,
                    CharacteristicNature::ActionValue->value,
                ]);
        } else {
            $query->where('group', 'object');
        }

        $characteristics = $query->get(['id', 'key', 'name', 'short_name', 'nature']);

        return [
            'objectEffectCharacteristics' => $characteristics,
            'objectEffectMonsters' => [],
        ];
    }

    /**
     * Props Inertia : listes + effets déjà enregistrés pour l’entité (item, consommable, ressource).
     *
     * @return array<string, mixed>
     */
    public static function inertiaPropsFor(Model $parent, ?Request $request = null): array
    {
        $req = $request ?? request();

        return array_merge(self::toArray($parent), [
            'objectEffects' => ObjectEffectResource::collection(
                $parent->objectEffects()->with(['characteristic', 'monster.creature'])->orderBy('id')->get()
            )->toArray($req),
        ]);
    }
}
