<?php

namespace App\Http\Controllers\Api;

use App\Enums\EntityState;
use App\Http\Controllers\Controller;
use App\Models\Entity\Breed;
use App\Support\Entity\AccessLevelMutationGuard;
use App\Support\Entity\EntityStateGate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * API Bulk update pour les breeds (affichées « Classes »).
 *
 * PATCH /api/entities/breeds/bulk
 */
class BreedBulkController extends Controller
{
    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->authorize('updateAny', Breed::class);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'min:1', 'exists:breeds,id'],

            'life_dice' => ['sometimes', 'nullable', 'string', 'max:255'],
            'specificity' => ['sometimes', 'nullable', 'string'],
            'mechanics' => ['sometimes', 'nullable', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'dofusdb_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'state' => ['sometimes', 'nullable', 'string', EntityState::rule()],
            'read_level' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:5'],
            'auto_update' => ['sometimes', 'nullable', 'boolean'],
        ]);
        $validated = AccessLevelMutationGuard::stripUnlessAdmin($request->user(), $validated);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
        if (count($ids) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Sélection invalide.',
            ], 422);
        }

        $patch = [];
        foreach ([
            'life_dice',
            'specificity',
            'mechanics',
            'description',
            'dofusdb_id',
            'state',
            'read_level',
            'write_level',
            'auto_update',
        ] as $k) {
            if (array_key_exists($k, $validated)) {
                $patch[$k] = $validated[$k];
            }
        }

        if (empty($patch)) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun champ à mettre à jour.',
            ], 422);
        }

        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            $models = Breed::query()->whereIn('id', $ids)->get();

            foreach ($ids as $id) {
                $model = $models->firstWhere('id', $id);
                if (! $model) {
                    $errors[] = ['id' => $id, 'error' => 'Not found'];

                    continue;
                }

                try {
                    $this->authorize('update', $model);
                    EntityStateGate::authorizeHttpTransition($request->user(), $model, $patch['state'] ?? null);
                    foreach ($patch as $k => $v) {
                        $model->{$k} = $v;
                    }
                    $model->save();
                    $updated++;
                } catch (\Throwable $e) {
                    $errors[] = ['id' => $id, 'error' => $e->getMessage()];
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour en masse.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => count($errors) === 0,
            'summary' => [
                'requested' => count($ids),
                'updated' => $updated,
                'errors' => count($errors),
            ],
            'errors' => $errors,
        ]);
    }
}
