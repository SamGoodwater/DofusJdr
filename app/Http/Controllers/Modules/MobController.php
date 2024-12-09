<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\CreatureFilterRequest;
use App\Http\Requests\Modules\MobFilterRequest;
use App\Events\NotificationSuperAdminEvent;
use App\Models\Modules\Mob;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class MobController extends Controller
{
    use AuthorizesRequests;

    public function index(MobFilterRequest $request): \Inertia\Response
    {
        $this->authorize('viewAny', Mob::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $mobs = Mob::paginate($paginationMaxDisplay);

        return Inertia::render('mob.index', [
            'mobs' => $mobs,
        ]);
    }

    public function show(Mob $mob, MobFilterRequest $request): \Inertia\Response
    {
        $this->authorize('view', $mob);

        return Inertia::render('Mobs/Show', [
            'ressources' => $mob->ressources,
            'panoply' => $mob->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Mob::class);

        return Inertia::render('mob.create');
    }

    public function store(MobFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', Mob::class);

        $data = DataService::extractData($request, new Mob(), [
            [
                'disk' => 'modules',
                'path_name' => 'mobs',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $mob = Mob::create($data);
        $mob->spells()->sync($request->input('spells'));
        $mob->ressources()->sync($request->input('ressources'));
        $mob->attributes()->sync($request->input('attributes'));
        $mob->items()->sync($request->input('items'));
        $mob->capabilities()->sync($request->input('capabilities'));
        $mob->consumables()->sync($request->input('consumables'));

        event(new NotificationSuperAdminEvent('mob', 'create',  $mob));

        return redirect()->route('mob.show', ['mob' => $mob]);
    }

    public function edit(Mob $mob): \Inertia\Response
    {
        $this->authorize('update', $mob);

        return Inertia::render('mob.edit', [
            'mob' => $mob,
            'ressources' => $mob->ressources,
            'panoply' => $mob->panoply,
        ]);
    }

    public function update(Mob $mob, MobFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $mob);
        $old_mob = $mob;

        $data = DataService::extractData($request, $mob, [
            [
                'disk' => 'modules',
                'path_name' => 'mobs',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $mob->update($data);
        $mob->spells()->sync($request->input('spells'));
        $mob->ressources()->sync($request->input('ressources'));
        $mob->attributes()->sync($request->input('attributes'));
        $mob->items()->sync($request->input('items'));
        $mob->capabilities()->sync($request->input('capabilities'));
        $mob->consumables()->sync($request->input('consumables'));

        event(new NotificationSuperAdminEvent('mob', "update", $mob, $old_mob));

        return redirect()->route('mob.show', ['mob' => $mob]);
    }

    public function delete(Mob $mob): RedirectResponse
    {
        $this->authorize('delete', $mob);
        event(new NotificationSuperAdminEvent('mob', "delete", $mob));
        $mob->delete();

        return redirect()->route('mob.index');
    }

    public function forceDelete(Mob $mob): RedirectResponse
    {
        $this->authorize('forceDelete', $mob);

        $mob->spells()->detach();
        $mob->ressources()->detach();
        $mob->attributes()->detach();
        $mob->items()->detach();
        $mob->capabilities()->detach();
        $mob->consumables()->detach();

        DataService::deleteFile($mob, 'image');
        event(new NotificationSuperAdminEvent('mob', "forced_delete", $mob));
        $mob->forceDelete();

        return redirect()->route('mob.index');
    }

    public function restore(Mob $mob): RedirectResponse
    {
        $this->authorize('restore', $mob);

        $mob->restore();

        return redirect()->route('mob.index');
    }
}
