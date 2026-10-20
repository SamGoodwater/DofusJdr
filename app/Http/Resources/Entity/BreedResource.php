<?php

namespace App\Http\Resources\Entity;

use App\Http\Resources\SectionResource;
use App\Models\Entity\Breed;
use App\Models\Entity\Spell;
use App\Services\Characteristic\CharacteristicMetaByDbColumnService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection as BaseCollection;

/**
 * Resource API/Frontend pour l'entité Breed (affichée « Classe »).
 */
class BreedResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id' => $this->id,
            'official_id' => $this->official_id,
            'dofusdb_id' => $this->dofusdb_id,
            'name' => $this->name,
            'description_fast' => $this->description_fast,
            'description' => $this->description,
            'evolution' => $this->evolution,
            'life_dice' => $this->life_dice,
            'life_dice_meta' => $this->lifeDiceCharacteristicMeta(),
            'specificity' => $this->specificity,
            'mechanics' => $this->mechanics,
            'dofus_version' => $this->dofus_version,
            'state' => $this->state,
            'read_level' => (int) ($this->read_level ?? 0),
            'write_level' => (int) ($this->write_level ?? 0),
            'image' => $this->image,
            'icon' => $this->icon,
            'symbol_full' => $this->symbol_full,
            'symbol_bw' => $this->symbol_bw,
            'logo_male' => $this->logo_male,
            'logo_female' => $this->logo_female,
            'image_full_male' => $this->image_full_male,
            'image_full_female' => $this->image_full_female,
            'auto_update' => $this->auto_update,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            'createdBy' => $this->whenLoaded('createdBy'),
            'npcs' => $this->whenLoaded('npcs'),
            'spells' => $this->whenLoaded('spells'),
            'capabilities' => $this->whenLoaded('capabilities', fn () => CapabilityResource::collection($this->capabilities)->resolve($request)),
            'creatureTraits' => $this->whenLoaded('creatureTraits', fn () => CreatureTraitResource::collection($this->creatureTraits)->resolve($request)),
            'languages' => $this->whenLoaded('languages', fn () => LanguageResource::collection($this->languages)->resolve($request)),
            'sections' => $this->whenLoaded('sections', fn () => SectionResource::collection($this->sections)->resolve($request)),
            'spell_slots' => $this->when(
                $this->relationLoaded('spells'),
                fn () => $this->formatSpellSlots($request)
            ),

            'element_orientations' => $this->when(
                $this->relationLoaded('elementOrientations'),
                function () {
                    /** @var Breed $breed */
                    $breed = $this->resource;

                    return $breed->elementOrientationsMap();
                }
            ),

            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'publish' => $user ? $user->can('publish', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'view' => $user ? $user->can('view', $this->resource) : false,
            ],
        ];
    }

    /**
     * Métadonnées caractéristique « dé de vie » (icône, couleur, surcharges par valeur).
     *
     * @return array<string, mixed>|null
     */
    protected function lifeDiceCharacteristicMeta(): ?array
    {
        try {
            $map = app(CharacteristicMetaByDbColumnService::class)->buildCreatureByDbColumn();

            return $map['life_dice'] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Emplacements de sorts groupés (niveau PJ + slot + choix).
     *
     * @return list<array{character_level: int, slot_index: int, spells: list<array<string, mixed>>}>
     */
    protected function formatSpellSlots(Request $request): array
    {
        /** @var Breed $breed */
        $breed = $this->resource;
        $grouped = $breed->getSpellSlotsGrouped();

        return BaseCollection::make($grouped)
            ->map(function (array $slot) use ($request): array {
                /** @var Collection<int, Spell> $spells */
                $spells = $slot['spells'];

                return [
                    'character_level' => $slot['character_level'],
                    'slot_index' => $slot['slot_index'],
                    'spells' => SpellResource::collection($spells)->resolve($request),
                ];
            })
            ->values()
            ->all();
    }
}
