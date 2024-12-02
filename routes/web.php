<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ScenarioController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

$uniqidRegex = '[A-Za-z0-9]+';
$slugRegex = '[A-Za-z0-9]+(?:(-|_).[A-Za-z0-9]+)*';

Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::prefix('page')->name("page.")->controller(PageController::class)->group(function () use ($slugRegex, $uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{page:slug}', 'show')->name('show')->where('page', $slugRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{page:slug}/edit', 'edit')->name('edit')->where('page', $slugRegex);
    Route::patch('/{page:uniqid}', 'update')->name('update')->where('page', $uniqidRegex);
    Route::delete('/{page:uniqid}', 'delete')->name('delete')->where('page', $uniqidRegex);
    Route::post('/{page:uniqid}', 'restore')->name('restore')->where('page', $uniqidRegex);
    Route::delete('/{page:uniqid}', 'forcedDelete')->name('forcedDelete')->where('page', $uniqidRegex);
});

Route::prefix("section")->name("section.")->controller(SectionController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{section:uniqid}', 'show')->name('show')->where('section', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{section:uniqid}/edit', 'edit')->name('edit')->where('section', $uniqidRegex);
    Route::patch('/{section:uniqid}', 'update')->name('update')->where('section', $uniqidRegex);
    Route::delete('/{section:uniqid}', 'delete')->name('delete')->where('section', $uniqidRegex);
    Route::post('/{section:uniqid}', 'restore')->name('restore')->where('section', $uniqidRegex);
    Route::delete('/{section:uniqid}', 'forcedDelete')->name('forcedDelete')->where('section', $uniqidRegex);
});

Route::prefix('campaign')->name("campaign.")->controller(CampaignController::class)->group(function () use ($uniqidRegex, $slugRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{campaign:slug}', 'show')->name('show')->where('campaign', $slugRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{campaign:slug}/edit', 'edit')->name('edit')->where('campaign', $slugRegex);
    Route::patch('/{campaign:uniqid}', 'update')->name('update')->where('campaign', $uniqidRegex);
    Route::delete('/{campaign:uniqid}', 'delete')->name('delete')->where('campaign', $uniqidRegex);
    Route::post('/{campaign:uniqid}', 'restore')->name('restore')->where('campaign', $uniqidRegex);
    Route::delete('/{campaign:uniqid}', 'forcedDelete')->name('forcedDelete')->where('campaign', $uniqidRegex);
});

Route::prefix('scenario')->name("scenario.")->controller(ScenarioController::class)->group(function () use ($uniqidRegex, $slugRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{scenario:slug}', 'show')->name('show')->where('scenario', $slugRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{scenario:slug}/edit', 'edit')->name('edit')->where('scenario', $slugRegex);
    Route::patch('/{scenario:uniqid}', 'update')->name('update')->where('scenario', $uniqidRegex);
    Route::delete('/{scenario:uniqid}', 'delete')->name('delete')->where('scenario', $uniqidRegex);
    Route::post('/{scenario:uniqid}', 'restore')->name('restore')->where('scenario', $uniqidRegex);
    Route::delete('/{scenario:uniqid}', 'forcedDelete')->name('forcedDelete')->where('scenario', $uniqidRegex);
});

Route::prefix('item')->name("item.")->controller(ItemController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{item:uniqid}', 'show')->name('show')->where('item', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{item:uniqid}/edit', 'edit')->name('edit')->where('item', $uniqidRegex);
    Route::patch('/{item:uniqid}', 'update')->name('update')->where('item', $uniqidRegex);
    Route::delete('/{item:uniqid}', 'delete')->name('delete')->where('item', $uniqidRegex);
    Route::post('/{item:uniqid}', 'restore')->name('restore')->where('item', $uniqidRegex);
    Route::delete('/{item:uniqid}', 'forcedDelete')->name('forcedDelete')->where('item', $uniqidRegex);
});




Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('auth')->name("auth.")->middleware('guest')->group(function () {
    Route::get('/login', [ProfileController::class, 'login'])->name('login');
    Route::get('/register', [ProfileController::class, 'register'])->name('register');
});

Route::middleware(['auth', 'verified'])->name('profile.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('destroy');
});

require __DIR__ . '/auth.php';
