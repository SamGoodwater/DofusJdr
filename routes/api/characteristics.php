<?php

use App\Http\Controllers\Api\CharacteristicCardController;
use App\Http\Controllers\Api\CharacteristicController;
use App\Http\Controllers\Api\CharacteristicNormsCatalogController;
use App\Http\Controllers\Api\CharacteristicNormsController;
use App\Http\Controllers\Api\CharacteristicReferenceTableController;
use App\Http\Controllers\Api\EquipmentBonusTableController;
use App\Http\Controllers\Api\ForgemagieRuneTableController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Caractéristiques (métadonnées pour le frontend)
|--------------------------------------------------------------------------
|
| Chargement au démarrage pour résolution icon, color, name, etc.
|
*/

Route::middleware(['web'])->get('/characteristics', [CharacteristicController::class, 'index'])
    ->name('api.characteristics.index');

Route::middleware(['web'])->get('/characteristics/norms-catalog/{group}/{entity?}', [CharacteristicNormsCatalogController::class, 'show'])
    ->where('entity', '.*')
    ->name('api.characteristics.norms-catalog');

Route::middleware(['web'])->get('/characteristics/{key}/card', [CharacteristicCardController::class, 'show'])
    ->name('api.characteristics.card');

Route::middleware(['web'])->get('/characteristics/{key}/norms/{entity?}', [CharacteristicNormsController::class, 'show'])
    ->name('api.characteristics.norms');

Route::middleware(['web'])->get('/characteristics/reference-table', [CharacteristicReferenceTableController::class, 'index'])
    ->name('api.characteristics.reference-table');

Route::middleware(['web'])->get('/characteristics/equipment-bonus-table', [EquipmentBonusTableController::class, 'index'])
    ->name('api.characteristics.equipment-bonus-table');

Route::middleware(['web'])->get('/characteristics/forgemagie-rune-table', [ForgemagieRuneTableController::class, 'index'])
    ->name('api.characteristics.forgemagie-rune-table');
