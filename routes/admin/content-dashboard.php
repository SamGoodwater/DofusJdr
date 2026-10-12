<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ContentBalanceController;
use App\Http\Controllers\Admin\ContentDofusdbWorkshopController;
use App\Http\Controllers\Admin\ContentManagementDashboardController;
use App\Http\Controllers\Admin\ContentTypeRegistryController;
use App\Http\Controllers\Admin\EntityPricesRecalculateController;
use App\Http\Controllers\Admin\RulesDownloadsController;
use Illuminate\Support\Facades\Route;

/**
 * Gestion du contenu : vue d’ensemble + types (MJ+) ; atelier DofusDB / jobs (admin+).
 */
Route::prefix('admin/content')
    ->name('admin.content.')
    ->middleware(['auth', 'content.area', 'password.confirm'])
    ->group(function () {
        Route::get('/', ContentManagementDashboardController::class)->name('dashboard.index');
        Route::get('/balance', [ContentBalanceController::class, 'index'])->name('balance.index');
        Route::get('/types', [ContentTypeRegistryController::class, 'index'])->name('types.index');
        Route::get('/types/{kind}', [ContentTypeRegistryController::class, 'show'])
            ->whereIn('kind', ContentTypeRegistryController::KINDS)
            ->name('types.show');

        Route::middleware(['role:admin'])->group(function () {
            Route::post('/rules-downloads', [RulesDownloadsController::class, 'store'])
                ->middleware(['throttle:6,1'])
                ->name('rules-downloads.run');
            Route::post('/entity-prices', [EntityPricesRecalculateController::class, 'store'])
                ->middleware(['throttle:6,1'])
                ->name('entity-prices.run');
            Route::get('/dofusdb', [ContentDofusdbWorkshopController::class, 'index'])->name('dofusdb.index');
            Route::post('/dofusdb/sync', [ContentDofusdbWorkshopController::class, 'sync'])
                ->middleware(['throttle:6,1'])
                ->name('dofusdb.sync');
        });
    });
