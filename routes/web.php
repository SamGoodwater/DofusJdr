<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::prefix('page')->name("page.")->controller(PageController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{page:slug}', 'show')->name('show')->where('page', '^[A-Za-z0-9]+(?:(-|_).[A-Za-z0-9]+)*$');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{page:slug}/edit', 'edit')->name('edit');
    Route::patch('/{page:slug}', 'update')->name('update');
    Route::delete('/{page:slug}', 'delete')->name('delete');
});

Route::prefix("section")->name("section.")->controller(SectionController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{section:uniqid}', 'show')->name('show')->where('section', '[A-Za-z0-9]+');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{section:uniqid}/edit', 'edit')->name('edit');
    Route::patch('/{section:uniqid}', 'update')->name('update');
    Route::delete('/{section:uniqid}', 'delete')->name('delete');
});

Route::prefix('item')->name("item.")->controller(ItemController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{item:uniqid}', 'show')->name('show')->where('item', '[A-Za-z0-9]+');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{item:uniqid}/edit', 'edit')->name('edit');
    Route::patch('/{item:uniqid}', 'update')->name('update');
    Route::delete('/{item:uniqid}', 'delete')->name('delete');
});





Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('auth')->name("auth.")->group(function () {
    Route::get('/login', [ProfileController::class, 'login'])->name('login');
    Route::get('/register', [ProfileController::class, 'register'])->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
