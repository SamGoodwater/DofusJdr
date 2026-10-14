<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Characteristic;
use App\Services\Characteristic\Card\CharacteristicCardService;
use App\Support\Characteristics\CharacteristicGroupRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Fiche publique d'une caractéristique pour tooltips joueur et panneau MJ.
 */
class CharacteristicCardController extends Controller
{
    public function __construct(
        private readonly CharacteristicCardService $cards,
    ) {}

    public function show(Request $request, string $key): JsonResponse
    {
        $characteristic = Characteristic::query()->where('key', $key)->first();
        if ($characteristic === null) {
            return response()->json(['error' => 'Caractéristique introuvable.'], 404);
        }

        $group = $request->query('group');
        $entity = $request->query('entity', '*');
        $entity = is_string($entity) && $entity !== '' ? $entity : '*';

        if (! is_string($group) || $group === '') {
            $guessed = CharacteristicGroupRegistry::findFirstPivot($characteristic->id, $entity);
            if ($guessed === null) {
                return response()->json(['error' => 'Aucune définition pour cette caractéristique.'], 404);
            }
            [$group] = $guessed;
        } elseif (! CharacteristicGroupRegistry::isValid($group)) {
            return response()->json([
                'error' => 'Groupe invalide. Utiliser creature, object, spell, consumable ou profile.',
            ], 422);
        }

        try {
            $card = $this->cards->build($key, $group, $entity);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        if ($card === null) {
            return response()->json(['error' => 'Aucune définition pour cette caractéristique.'], 404);
        }

        return response()->json(['card' => $card]);
    }
}
