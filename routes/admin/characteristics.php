<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\CharacteristicController;
use Illuminate\Support\Facades\Route;

/**
 * Administration des caractéristiques (contenu de jeu, MJ+).
 *
 * Lecture + mutations métier : game_master + content.area + password.confirm.
 * Liaisons scrapping de masse : admin+.
 * Export / import seeders : CLI uniquement (`project:backup` hors prod, `project:seed --overwrite`).
 */
Route::prefix('admin/content/characteristics')
    ->name('admin.characteristics.')
    ->middleware(['auth', 'role:game_master', 'content.area', 'password.confirm'])
    ->group(function () {
        Route::get('/', [CharacteristicController::class, 'index'])->name('index');
        Route::get('/create', [CharacteristicController::class, 'create'])->name('create');
        Route::get('/formula-preview', [CharacteristicController::class, 'formulaPreview'])->name('formula-preview');
        Route::get('/{characteristic_key}', [CharacteristicController::class, 'show'])->name('show')->where('characteristic_key', '[a-z0-9_]+');
        Route::get('/{characteristic_key}/scrapping-mapping-options', [CharacteristicController::class, 'scrappingMappingOptions'])->name('scrapping-mapping-options')->where('characteristic_key', '[a-z0-9_]+');

        Route::post('/', [CharacteristicController::class, 'store'])->name('store');
        Route::post('/suggest-conversion-formula', [CharacteristicController::class, 'suggestConversionFormula'])->name('suggest-conversion-formula');
        Route::post('/upload-icon', [CharacteristicController::class, 'uploadIcon'])->name('upload-icon');
        Route::post('/{characteristic_key}/norms/preview', [CharacteristicController::class, 'normsPreview'])->name('norms-preview')->where('characteristic_key', '[a-z0-9_]+');
        Route::patch('/{characteristic_key}', [CharacteristicController::class, 'update'])->name('update')->where('characteristic_key', '[a-z0-9_]+');
        Route::delete('/{characteristic_key}', [CharacteristicController::class, 'destroy'])->name('destroy')->where('characteristic_key', '[a-z0-9_]+');

        Route::middleware(['role:admin'])->group(function () {
            Route::post('/{characteristic_key}/store-scrapping-mapping', [CharacteristicController::class, 'storeScrappingMapping'])->name('store-scrapping-mapping')->where('characteristic_key', '[a-z0-9_]+');
            Route::post('/{characteristic_key}/unlink-scrapping-mapping', [CharacteristicController::class, 'unlinkScrappingMapping'])->name('unlink-scrapping-mapping')->where('characteristic_key', '[a-z0-9_]+');
        });
    });
