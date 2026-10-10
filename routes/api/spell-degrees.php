<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Spell\SpellDegreeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Spell degrees — progression native d’un sort
|--------------------------------------------------------------------------
*/
Route::prefix('spells')->group(function () {
    Route::middleware('web')->group(function () {
        Route::get('{spell}/degrees', [SpellDegreeController::class, 'index'])
            ->name('spells.degrees.index');
    });

    Route::middleware(['web', 'auth'])->group(function () {
        Route::post('{spell}/degrees', [SpellDegreeController::class, 'store'])
            ->name('spells.degrees.store');
        Route::put('{spell}/degrees', [SpellDegreeController::class, 'syncBulk'])
            ->name('spells.degrees.sync-bulk');
        Route::match(['put', 'patch'], '{spell}/degrees/{degree}', [SpellDegreeController::class, 'update'])
            ->name('spells.degrees.update');
        Route::delete('{spell}/degrees/{degree}', [SpellDegreeController::class, 'destroy'])
            ->name('spells.degrees.destroy');
        Route::post('{spell}/degrees/{degree}/materialize-effects', [SpellDegreeController::class, 'materializeEffects'])
            ->name('spells.degrees.materialize-effects');
        Route::put('{spell}/degrees/{degree}/effects', [SpellDegreeController::class, 'syncEffects'])
            ->name('spells.degrees.effects.sync');
    });
});
