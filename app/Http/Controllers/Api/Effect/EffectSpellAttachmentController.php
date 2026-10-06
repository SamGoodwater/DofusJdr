<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Effect;

use App\Http\Controllers\Controller;
use App\Http\Requests\Effect\CreateSpellEffectRequest;
use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\Entity\Spell;
use App\Services\Effect\EffectGroupUpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Liaison sort ↔ définition d’effet (pivot effect_spell).
 */
class EffectSpellAttachmentController extends Controller
{
    /**
     * Crée une définition (premier degré) et la lie au sort.
     *
     * @example POST /api/effects/spell-effects { spell_id, name, target_type?, initial_sub_effects? }
     */
    public function createForSpell(CreateSpellEffectRequest $request, EffectGroupUpdateService $updater): JsonResponse
    {
        $spell = Spell::query()->findOrFail($request->integer('spell_id'));
        $this->authorize('update', $spell);

        $effect = DB::transaction(function () use ($request, $spell, $updater): Effect {
            $effect = Effect::query()->create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'target_type' => $request->input('target_type') ?: Effect::TARGET_DIRECT,
            ]);
            $degree = EffectDegree::query()->create([
                'effect_id' => $effect->id,
                'degree' => 1,
                'area' => $request->input('initial_area'),
                'required_creature_level' => $request->input('initial_required_creature_level'),
                'slug' => $request->input('initial_degree_slug'),
            ]);

            /** @var list<array<string, mixed>> $initialSubs */
            $initialSubs = $request->input('initial_sub_effects', []) ?: [];
            if ($initialSubs !== []) {
                $updater->syncSubEffects($degree, $initialSubs);
            }

            $spell->effects()->syncWithoutDetaching([$effect->id]);

            return $effect;
        });

        return response()->json([
            'data' => [
                'id' => $effect->id,
                'name' => $effect->name,
            ],
        ], 201);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'spell_id' => 'required|integer|exists:spells,id',
            'effect_id' => 'required|integer|exists:effects,id',
        ]);
        $spell = Spell::query()->findOrFail($data['spell_id']);
        $this->authorize('update', $spell);

        $effectId = (int) $data['effect_id'];
        $spell->effects()->syncWithoutDetaching([$effectId]);

        return response()->json(['ok' => true], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'spell_id' => 'required|integer|exists:spells,id',
            'effect_id' => 'required|integer|exists:effects,id',
        ]);
        $spell = Spell::query()->findOrFail($data['spell_id']);
        $this->authorize('update', $spell);

        $spell->effects()->detach((int) $data['effect_id']);

        return response()->json(null, 204);
    }
}
