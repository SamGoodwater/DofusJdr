<?php

use App\Http\Controllers\Modules\ItemController;
use App\Http\Controllers\Modules\CampaignController;
use App\Http\Controllers\Modules\ScenarioController;
use App\Http\Controllers\Modules\AttributeController;
use App\Http\Controllers\Modules\CapabilityController;
use App\Http\Controllers\Modules\ClasseController;
use App\Http\Controllers\Modules\ConditionController;
use App\Http\Controllers\Modules\ConsumableController;
use App\Http\Controllers\Modules\MobController;
use App\Http\Controllers\Modules\MobraceController;
use App\Http\Controllers\Modules\NpcController;
use App\Http\Controllers\Modules\PanoplyController;
use App\Http\Controllers\Modules\RessourceController;
use App\Http\Controllers\Modules\ShopController;
use App\Http\Controllers\Modules\SpecializationController;
use App\Http\Controllers\Modules\SpellController;
use App\Http\Controllers\Modules\SpelltypeController;
use App\Http\Controllers\Modules\ItemtypeController;
use App\Http\Controllers\Modules\ConsumabletypeController;
use App\Http\Controllers\Modules\RessourcetypeController;

use App\Http\Controllers\SectionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PageController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

$uniqidRegex = '[A-Za-z0-9]+';
$slugRegex = '[A-Za-z0-9]+(?:(-|_).[A-Za-z0-9]+)*';

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::prefix('auth')->name("auth.")->controller(LoginController::class)->middleware('guest')->group(function () {
    Route::get('/', 'connexion')->name('connexion');
    Route::get('/inscription', 'inscription')->name('inscription');
    Route::post('/login', 'login')->name('login');
    Route::post('/register', 'register')->name('register');
    Route::post('/logout', 'logout')->name('logout');
});

// Pages
Route::prefix('page')->name("page.")->controller(PageController::class)->group(function () use ($slugRegex, $uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{page:slug}', 'show')->name('show')->where('page', $slugRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{page:slug}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('page', $slugRegex);
    Route::patch('/{page:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('page', $uniqidRegex);
    Route::delete('/{page:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('page', $uniqidRegex);
    Route::post('/{page:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('page', $uniqidRegex);
    Route::delete('/{page:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('page', $uniqidRegex);
});

// Sections
Route::prefix("section")->name("section.")->controller(SectionController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{section:uniqid}', 'show')->name('show')->where('section', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{section:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
    Route::patch('/{section:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
    Route::delete('/{section:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
    Route::post('/{section:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
    Route::delete('/{section:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
});

// Campaigns
Route::prefix('campaign')->name("campaign.")->controller(CampaignController::class)->group(function () use ($uniqidRegex, $slugRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{campaign:slug}', 'show')->name('show')->where('campaign', $slugRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{campaign:slug}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('campaign', $slugRegex);
    Route::patch('/{campaign:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('campaign', $uniqidRegex);
    Route::delete('/{campaign:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('campaign', $uniqidRegex);
    Route::post('/{campaign:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('campaign', $uniqidRegex);
    Route::delete('/{campaign:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('campaign', $uniqidRegex);
});

// Scenarios
Route::prefix('scenario')->name("scenario.")->controller(ScenarioController::class)->group(function () use ($uniqidRegex, $slugRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{scenario:slug}', 'show')->name('show')->where('scenario', $slugRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{scenario:slug}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('scenario', $slugRegex);
    Route::patch('/{scenario:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('scenario', $uniqidRegex);
    Route::delete('/{scenario:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('scenario', $uniqidRegex);
    Route::post('/{scenario:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('scenario', $uniqidRegex);
    Route::delete('/{scenario:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('scenario', $uniqidRegex);
});

// Items
Route::prefix('item')->name("item.")->controller(ItemController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{item:uniqid}', 'show')->name('show')->where('item', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{item:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
    Route::patch('/{item:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
    Route::delete('/{item:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
    Route::post('/{item:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
    Route::delete('/{item:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
});

// Itemtypes
Route::prefix('itemtype')->name("itemtype.")->controller(ItemtypeController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{itemtype:uniqid}', 'show')->name('show')->where('itemtype', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{itemtype:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
    Route::patch('/{itemtype:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
    Route::delete('/{itemtype:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
    Route::post('/{itemtype:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
    Route::delete('/{itemtype:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
});

// Attributes
Route::prefix('attribute')->name("attribute.")->controller(AttributeController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{attribute:uniqid}', 'show')->name('show')->where('attribute', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{attribute:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
    Route::patch('/{attribute:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
    Route::delete('/{attribute:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
    Route::post('/{attribute:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
    Route::delete('/{attribute:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
});

// Capabilities
Route::prefix('capability')->name("capability.")->controller(CapabilityController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{capability:uniqid}', 'show')->name('show')->where('capability', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{capability:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
    Route::patch('/{capability:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
    Route::delete('/{capability:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
    Route::post('/{capability:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
    Route::delete('/{capability:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
});

// Classes
Route::prefix('classe')->name("classe.")->controller(ClasseController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{class:uniqid}', 'show')->name('show')->where('classe', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{classe:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
    Route::patch('/{classe:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
    Route::delete('/{classe:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
    Route::post('/{classe:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
    Route::delete('/{classe:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
});

//Conditions
Route::prefix('condition')->name("condition.")->controller(ConditionController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{condition:uniqid}', 'show')->name('show')->where('condition', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{condition:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
    Route::patch('/{condition:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
    Route::delete('/{condition:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
    Route::post('/{condition:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
    Route::delete('/{condition:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
});

// Consumables
Route::prefix('consumable')->name("consumable.")->controller(ConsumableController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{consumable:uniqid}', 'show')->name('show')->where('consumable', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{consumable:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
    Route::patch('/{consumable:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
    Route::delete('/{consumable:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
    Route::post('/{consumable:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
    Route::delete('/{consumable:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
});

// Cosumabletypes
Route::prefix('consumabletype')->name("consumabletype.")->controller(ConsumabletypeController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{consumabletype:uniqid}', 'show')->name('show')->where('consumabletype', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{consumabletype:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
    Route::patch('/{consumabletype:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
    Route::delete('/{consumabletype:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
    Route::post('/{consumabletype:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
    Route::delete('/{consumabletype:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
});

// Mobs
Route::prefix('mob')->name("mob.")->controller(MobController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{mob:uniqid}', 'show')->name('show')->where('mob', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{mob:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
    Route::patch('/{mob:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
    Route::delete('/{mob:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
    Route::post('/{mob:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
    Route::delete('/{mob:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
});

// Mobraces
Route::prefix('mobrace')->name("mobrace.")->controller(MobraceController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{mobrace:uniqid}', 'show')->name('show')->where('mobrace', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{mobrace:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
    Route::patch('/{mobrace:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
    Route::delete('/{mobrace:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
    Route::post('/{mobrace:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
    Route::delete('/{mobrace:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
});

// Npcs
Route::prefix('npc')->name("npc.")->controller(NpcController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{npc:uniqid}', 'show')->name('show')->where('npc', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{npc:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
    Route::patch('/{npc:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
    Route::delete('/{npc:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
    Route::post('/{npc:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
    Route::delete('/{npc:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
});

// Panoplies
Route::prefix('panoply')->name("panoply.")->controller(PanoplyController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{panoply:uniqid}', 'show')->name('show')->where('panoply', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{panoply:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
    Route::patch('/{panoply:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
    Route::delete('/{panoply:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
    Route::post('/{panoply:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
    Route::delete('/{panoply:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
});

// Ressources
Route::prefix('ressource')->name("ressource.")->controller(RessourceController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{ressource:uniqid}', 'show')->name('show')->where('ressource', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{ressource:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('ressource', $uniqidRegex);
    Route::patch('/{ressource:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('ressource', $uniqidRegex);
    Route::delete('/{ressource:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('ressource', $uniqidRegex);
    Route::post('/{ressource:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('ressource', $uniqidRegex);
    Route::delete('/{ressource:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('ressource', $uniqidRegex);
});

// RessourceTypes
Route::prefix('ressourcetype')->name("ressourcetype.")->controller(RessourcetypeController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{ressourcetype:uniqid}', 'show')->name('show')->where('ressourcetype', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{ressourcetype:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('ressourcetype', $uniqidRegex);
    Route::patch('/{ressourcetype:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('ressourcetype', $uniqidRegex);
    Route::delete('/{ressourcetype:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('ressourcetype', $uniqidRegex);
    Route::post('/{ressourcetype:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->middleware(['auth', 'verified'])->where('ressourcetype', $uniqidRegex);
    Route::delete('/{ressourcetype:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('ressourcetype', $uniqidRegex);
});

// Shops
Route::prefix('shop')->name("shop.")->controller(ShopController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{shop:uniqid}', 'show')->name('show')->where('shop', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{shop:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
    Route::patch('/{shop:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
    Route::delete('/{shop:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
    Route::post('/{shop:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
    Route::delete('/{shop:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
});

// Specializations
Route::prefix('specialization')->name("specialization.")->controller(SpecializationController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{specialization:uniqid}', 'show')->name('show')->where('specialization', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{specialization:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
    Route::patch('/{specialization:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
    Route::delete('/{specialization:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
    Route::post('/{specialization:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
    Route::delete('/{specialization:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
});

// Spells
Route::prefix('spell')->name("spell.")->controller(SpellController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{spell:uniqid}', 'show')->name('show')->where('spell', $uniqidRegex);
    Route::inertia('/create', 'create')->middleware(['auth', 'verified'])->name('create');
    Route::post('/', 'store')->middleware(['auth', 'verified'])->name('store');
    Route::inertia('/{spell:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
    Route::patch('/{spell:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
    Route::delete('/{spell:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
    Route::post('/{spell:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
    Route::delete('/{spell:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
});

// Spelltypes
Route::prefix('spelltype')->name("spelltype.")->controller(SpelltypeController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{spelltype:uniqid}', 'show')->name('show')->where('spelltype', $uniqidRegex);
    Route::inertia('/create', 'create')->middleware(['auth', 'verified'])->name('create');
    Route::post('/', 'store')->middleware(['auth', 'verified'])->name('store');
    Route::inertia('/{spelltype:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
    Route::patch('/{spelltype:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
    Route::delete('/{spelltype:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
    Route::post('/{spelltype:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
    Route::delete('/{spelltype:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
});

// Users
Route::prefix('user')->name("user.")->controller(UserController::class)->middleware(['auth', 'verified'])->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{user:uniqid}', 'show')->name('show')->where('user', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::inertia('/{user:uniqid}/edit', 'edit')->name('edit')->where('user', $uniqidRegex);
    Route::patch('/{user:uniqid}', 'update')->name('update')->where('user', $uniqidRegex);
    Route::delete('/{user:uniqid}', 'delete')->name('delete')->where('user', $uniqidRegex);
    Route::post('/{user:uniqid}', 'restore')->name('restore')->where('user', $uniqidRegex);
    Route::delete('/{user:uniqid}', 'forcedDelete')->name('forcedDelete')->where('user', $uniqidRegex);
});


// Système de gestion des images avec Glyde : https://grafikart.fr/tutoriels/image-resize-glide-php-1358
// Impossible d'installer glyde
// Route::get('/image/{path}', [App\Http\Controllers\Utilities\ImageController::class, 'show']);

require __DIR__ . '/auth.php';
