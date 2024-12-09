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
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

$uniqidRegex = '[A-Za-z0-9]+';
$slugRegex = '[A-Za-z0-9]+(?:(-|_).[A-Za-z0-9]+)*';

Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register')
    ]);
})->name('home');

// Pages
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

// Sections
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

// Campaigns
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

// Scenarios
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

// Items
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

// Itemtypes
Route::prefix('itemtype')->name("itemtype.")->controller(ItemtypeController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{itemtype:uniqid}', 'show')->name('show')->where('itemtype', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{itemtype:uniqid}/edit', 'edit')->name('edit')->where('itemtype', $uniqidRegex);
    Route::patch('/{itemtype:uniqid}', 'update')->name('update')->where('itemtype', $uniqidRegex);
    Route::delete('/{itemtype:uniqid}', 'delete')->name('delete')->where('itemtype', $uniqidRegex);
    Route::post('/{itemtype:uniqid}', 'restore')->name('restore')->where('itemtype', $uniqidRegex);
    Route::delete('/{itemtype:uniqid}', 'forcedDelete')->name('forcedDelete')->where('itemtype', $uniqidRegex);
});

// Attributes
Route::prefix('attribute')->name("attribute.")->controller(AttributeController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{attribute:uniqid}', 'show')->name('show')->where('attribute', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{attribute:uniqid}/edit', 'edit')->name('edit')->where('attribute', $uniqidRegex);
    Route::patch('/{attribute:uniqid}', 'update')->name('update')->where('attribute', $uniqidRegex);
    Route::delete('/{attribute:uniqid}', 'delete')->name('delete')->where('attribute', $uniqidRegex);
    Route::post('/{attribute:uniqid}', 'restore')->name('restore')->where('attribute', $uniqidRegex);
    Route::delete('/{attribute:uniqid}', 'forcedDelete')->name('forcedDelete')->where('attribute', $uniqidRegex);
});

// Capabilities
Route::prefix('capability')->name("capability.")->controller(CapabilityController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{capability:uniqid}', 'show')->name('show')->where('capability', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{capability:uniqid}/edit', 'edit')->name('edit')->where('capability', $uniqidRegex);
    Route::patch('/{capability:uniqid}', 'update')->name('update')->where('capability', $uniqidRegex);
    Route::delete('/{capability:uniqid}', 'delete')->name('delete')->where('capability', $uniqidRegex);
    Route::post('/{capability:uniqid}', 'restore')->name('restore')->where('capability', $uniqidRegex);
    Route::delete('/{capability:uniqid}', 'forcedDelete')->name('forcedDelete')->where('capability', $uniqidRegex);
});

// Classes
Route::prefix('classe')->name("classe.")->controller(ClasseController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{class:uniqid}', 'show')->name('show')->where('classe', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{classe:uniqid}/edit', 'edit')->name('edit')->where('classe', $uniqidRegex);
    Route::patch('/{classe:uniqid}', 'update')->name('update')->where('classe', $uniqidRegex);
    Route::delete('/{classe:uniqid}', 'delete')->name('delete')->where('classe', $uniqidRegex);
    Route::post('/{classe:uniqid}', '
    ')->name('restore')->where('classe', $uniqidRegex);
    Route::delete('/{classe:uniqid}', 'forcedDelete')->name('forcedDelete')->where('classe', $uniqidRegex);
});

//Conditions
Route::prefix('condition')->name("condition.")->controller(ConditionController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{condition:uniqid}', 'show')->name('show')->where('condition', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{condition:uniqid}/edit', 'edit')->name('edit')->where('condition', $uniqidRegex);
    Route::patch('/{condition:uniqid}', 'update')->name('update')->where('condition', $uniqidRegex);
    Route::delete('/{condition:uniqid}', 'delete')->name('delete')->where('condition', $uniqidRegex);
    Route::post('/{condition:uniqid}', 'restore')->name('restore')->where('condition', $uniqidRegex);
    Route::delete('/{condition:uniqid}', 'forcedDelete')->name('forcedDelete')->where('condition', $uniqidRegex);
});

// Consumables
Route::prefix('consumable')->name("consumable.")->controller(ConsumableController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{consumable:uniqid}', 'show')->name('show')->where('consumable', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{consumable:uniqid}/edit', 'edit')->name('edit')->where('consumable', $uniqidRegex);
    Route::patch('/{consumable:uniqid}', 'update')->name('update')->where('consumable', $uniqidRegex);
    Route::delete('/{consumable:uniqid}', 'delete')->name('delete')->where('consumable', $uniqidRegex);
    Route::post('/{consumable:uniqid}', 'restore')->name('restore')->where('consumable', $uniqidRegex);
    Route::delete('/{consumable:uniqid}', 'forcedDelete')->name('forcedDelete')->where('consumable', $uniqidRegex);
});

// Cosumabletypes
Route::prefix('consumabletype')->name("consumabletype.")->controller(ConsumabletypeController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{consumabletype:uniqid}', 'show')->name('show')->where('consumabletype', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{consumabletype:uniqid}/edit', 'edit')->name('edit')->where('consumabletype', $uniqidRegex);
    Route::patch('/{consumabletype:uniqid}', 'update')->name('update')->where('consumabletype', $uniqidRegex);
    Route::delete('/{consumabletype:uniqid}', 'delete')->name('delete')->where('consumabletype', $uniqidRegex);
    Route::post('/{consumabletype:uniqid}', 'restore')->name('restore')->where('consumabletype', $uniqidRegex);
    Route::delete('/{consumabletype:uniqid}', 'forcedDelete')->name('forcedDelete')->where('consumabletype', $uniqidRegex);
});

// Mobs
Route::prefix('mob')->name("mob.")->controller(MobController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{mob:uniqid}', 'show')->name('show')->where('mob', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{mob:uniqid}/edit', 'edit')->name('edit')->where('mob', $uniqidRegex);
    Route::patch('/{mob:uniqid}', 'update')->name('update')->where('mob', $uniqidRegex);
    Route::delete('/{mob:uniqid}', 'delete')->name('delete')->where('mob', $uniqidRegex);
    Route::post('/{mob:uniqid}', 'restore')->name('restore')->where('mob', $uniqidRegex);
    Route::delete('/{mob:uniqid}', 'forcedDelete')->name('forcedDelete')->where('mob', $uniqidRegex);
});

// Mobraces
Route::prefix('mobrace')->name("mobrace.")->controller(MobraceController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{mobrace:uniqid}', 'show')->name('show')->where('mobrace', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{mobrace:uniqid}/edit', 'edit')->name('edit')->where('mobrace', $uniqidRegex);
    Route::patch('/{mobrace:uniqid}', 'update')->name('update')->where('mobrace', $uniqidRegex);
    Route::delete('/{mobrace:uniqid}', 'delete')->name('delete')->where('mobrace', $uniqidRegex);
    Route::post('/{mobrace:uniqid}', 'restore')->name('restore')->where('mobrace', $uniqidRegex);
    Route::delete('/{mobrace:uniqid}', 'forcedDelete')->name('forcedDelete')->where('mobrace', $uniqidRegex);
});

// Npcs
Route::prefix('npc')->name("npc.")->controller(NpcController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{npc:uniqid}', 'show')->name('show')->where('npc', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{npc:uniqid}/edit', 'edit')->name('edit')->where('npc', $uniqidRegex);
    Route::patch('/{npc:uniqid}', 'update')->name('update')->where('npc', $uniqidRegex);
    Route::delete('/{npc:uniqid}', 'delete')->name('delete')->where('npc', $uniqidRegex);
    Route::post('/{npc:uniqid}', 'restore')->name('restore')->where('npc', $uniqidRegex);
    Route::delete('/{npc:uniqid}', 'forcedDelete')->name('forcedDelete')->where('npc', $uniqidRegex);
});

// Panoplies
Route::prefix('panoply')->name("panoply.")->controller(PanoplyController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{panoply:uniqid}', 'show')->name('show')->where('panoply', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{panoply:uniqid}/edit', 'edit')->name('edit')->where('panoply', $uniqidRegex);
    Route::patch('/{panoply:uniqid}', 'update')->name('update')->where('panoply', $uniqidRegex);
    Route::delete('/{panoply:uniqid}', 'delete')->name('delete')->where('panoply', $uniqidRegex);
    Route::post('/{panoply:uniqid}', 'restore')->name('restore')->where('panoply', $uniqidRegex);
    Route::delete('/{panoply:uniqid}', 'forcedDelete')->name('forcedDelete')->where('panoply', $uniqidRegex);
});

// Ressources
Route::prefix('ressource')->name("ressource.")->controller(RessourceController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{ressource:uniqid}', 'show')->name('show')->where('ressource', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{ressource:uniqid}/edit', 'edit')->name('edit')->where('ressource', $uniqidRegex);
    Route::patch('/{ressource:uniqid}', 'update')->name('update')->where('ressource', $uniqidRegex);
    Route::delete('/{ressource:uniqid}', 'delete')->name('delete')->where('ressource', $uniqidRegex);
    Route::post('/{ressource:uniqid}', 'restore')->name('restore')->where('ressource', $uniqidRegex);
    Route::delete('/{ressource:uniqid}', 'forcedDelete')->name('forcedDelete')->where('ressource', $uniqidRegex);
});

// RessourceTypes
Route::prefix('ressourcetype')->name("ressourcetype.")->controller(RessourcetypeController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{ressourcetype:uniqid}', 'show')->name('show')->where('ressourcetype', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{ressourcetype:uniqid}/edit', 'edit')->name('edit')->where('ressourcetype', $uniqidRegex);
    Route::patch('/{ressourcetype:uniqid}', 'update')->name('update')->where('ressourcetype', $uniqidRegex);
    Route::delete('/{ressourcetype:uniqid}', 'delete')->name('delete')->where('ressourcetype', $uniqidRegex);
    Route::post('/{ressourcetype:uniqid}', 'restore')->name('restore')->where('ressourcetype', $uniqidRegex);
    Route::delete('/{ressourcetype:uniqid}', 'forcedDelete')->name('forcedDelete')->where('ressourcetype', $uniqidRegex);
});

// Shops
Route::prefix('shop')->name("shop.")->controller(ShopController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{shop:uniqid}', 'show')->name('show')->where('shop', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{shop:uniqid}/edit', 'edit')->name('edit')->where('shop', $uniqidRegex);
    Route::patch('/{shop:uniqid}', 'update')->name('update')->where('shop', $uniqidRegex);
    Route::delete('/{shop:uniqid}', 'delete')->name('delete')->where('shop', $uniqidRegex);
    Route::post('/{shop:uniqid}', 'restore')->name('restore')->where('shop', $uniqidRegex);
    Route::delete('/{shop:uniqid}', 'forcedDelete')->name('forcedDelete')->where('shop', $uniqidRegex);
});

// Specializations
Route::prefix('specialization')->name("specialization.")->controller(SpecializationController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{specialization:uniqid}', 'show')->name('show')->where('specialization', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{specialization:uniqid}/edit', 'edit')->name('edit')->where('specialization', $uniqidRegex);
    Route::patch('/{specialization:uniqid}', 'update')->name('update')->where('specialization', $uniqidRegex);
    Route::delete('/{specialization:uniqid}', 'delete')->name('delete')->where('specialization', $uniqidRegex);
    Route::post('/{specialization:uniqid}', 'restore')->name('restore')->where('specialization', $uniqidRegex);
    Route::delete('/{specialization:uniqid}', 'forcedDelete')->name('forcedDelete')->where('specialization', $uniqidRegex);
});

// Spells
Route::prefix('spell')->name("spell.")->controller(SpellController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{spell:uniqid}', 'show')->name('show')->where('spell', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{spell:uniqid}/edit', 'edit')->name('edit')->where('spell', $uniqidRegex);
    Route::patch('/{spell:uniqid}', 'update')->name('update')->where('spell', $uniqidRegex);
    Route::delete('/{spell:uniqid}', 'delete')->name('delete')->where('spell', $uniqidRegex);
    Route::post('/{spell:uniqid}', 'restore')->name('restore')->where('spell', $uniqidRegex);
    Route::delete('/{spell:uniqid}', 'forcedDelete')->name('forcedDelete')->where('spell', $uniqidRegex);
});

// Spelltypes
Route::prefix('spelltype')->name("spelltype.")->controller(SpelltypeController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{spelltype:uniqid}', 'show')->name('show')->where('spelltype', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{spelltype:uniqid}/edit', 'edit')->name('edit')->where('spelltype', $uniqidRegex);
    Route::patch('/{spelltype:uniqid}', 'update')->name('update')->where('spelltype', $uniqidRegex);
    Route::delete('/{spelltype:uniqid}', 'delete')->name('delete')->where('spelltype', $uniqidRegex);
    Route::post('/{spelltype:uniqid}', 'restore')->name('restore')->where('spelltype', $uniqidRegex);
    Route::delete('/{spelltype:uniqid}', 'forcedDelete')->name('forcedDelete')->where('spelltype', $uniqidRegex);
});

// Users
Route::prefix('user')->name("user.")->controller(UserController::class)->group(function () use ($uniqidRegex) {
    Route::get('/', 'index')->name('index');
    Route::get('/{user:uniqid}', 'show')->name('show')->where('user', $uniqidRegex);
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{user:uniqid}/edit', 'edit')->name('edit')->where('user', $uniqidRegex);
    Route::patch('/{user:uniqid}', 'update')->name('update')->where('user', $uniqidRegex);
    Route::delete('/{user:uniqid}', 'delete')->name('delete')->where('user', $uniqidRegex);
    Route::post('/{user:uniqid}', 'restore')->name('restore')->where('user', $uniqidRegex);
    Route::delete('/{user:uniqid}', 'forcedDelete')->name('forcedDelete')->where('user', $uniqidRegex);
});


// Système de gestion des images avec Glyde : https://grafikart.fr/tutoriels/image-resize-glide-php-1358
// Impossible d'installer glyde
// Route::get('/image/{path}', [App\Http\Controllers\Utilities\ImageController::class, 'show']);


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
