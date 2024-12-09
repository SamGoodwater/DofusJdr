<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\ScenarioFilterRequest;
use App\Events\NotificationSuperAdminEvent;
use App\Models\Modules\Scenario;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class ScenarioController extends Controller
{
    use AuthorizesRequests;

    public function index(ScenarioFilterRequest $request): \Inertia\Response
    {
        $this->authorize('viewAny', Scenario::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $scenarios = Scenario::paginate($paginationMaxDisplay);

        return Inertia::render('scenario.index', [
            'scenarios' => $scenarios,
        ]);
    }

    public function show(Scenario $scenario, ScenarioFilterRequest $request): \Inertia\Response
    {
        $this->authorize('view', $scenario);

        return Inertia::render('Scenarios/Show', [
            'ressources' => $scenario->ressources,
            'panoply' => $scenario->panoply,
            'files' => $scenario->getPathFiles(),
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Scenario::class);

        return Inertia::render('scenario.create');
    }

    public function store(ScenarioFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', Scenario::class);

        $data = DataService::extractData($request, new Scenario(), [
            [
                'disk' => 'modules',
                'path_name' => 'scenarios',
                'name_bd' => 'image',
                'is_multiple_files' => false, // si true, alors le fichier est un tableau de fichiers
                'compress' => true
            ],
            [
                'disk' => 'modules',
                'path_name' => 'scenarios',
                'name_bd' => 'file',
                'is_multiple_files' => true, // si true, alors le fichier est un tableau de fichiers
                'compress' => false
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $scenario = Scenario::create($data);
        $scenario->spells()->sync($request->validated('spells'));
        $scenario->mobs()->sync($request->validated('mobs'));
        $scenario->npcs()->sync($request->validated('npcs'));
        $scenario->items()->sync($request->validated('items'));
        $scenario->shops()->sync($request->validated('shops'));
        $scenario->ressources()->sync($request->validated('ressources'));
        $scenario->consumables()->sync($request->validated('consumables'));
        $scenario->panoplies()->sync($request->validated('panoplies'));
        $scenario->pages()->sync($request->validated('pages'));

        event(new NotificationSuperAdminEvent('scenario', 'create',  $scenario));

        return redirect()->route('scenario.show', ['scenario' => $scenario]);
    }

    public function edit(Scenario $scenario): \Inertia\Response
    {
        $this->authorize('update', $scenario);

        return Inertia::render('scenario.edit', [
            'scenario' => $scenario,
            'ressources' => $scenario->ressources,
            'files' => $scenario->getPathFiles(),
        ]);
    }

    public function update(Scenario $scenario, ScenarioFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $scenario);
        $old_scenario = $scenario;

        $data = DataService::extractData($request, $scenario, [
            [
                'disk' => 'modules',
                'path_name' => 'scenarios',
                'name_bd' => 'image',
                'is_multiple_files' => false, // si true, alors le fichier est un tableau de fichiers
                'compress' => true
            ],
            [
                'disk' => 'modules',
                'path_name' => 'scenarios',
                'name_bd' => 'file',
                'is_multiple_files' => true, // si true, alors le fichier est un tableau de fichiers
                'compress' => false
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $scenario->update($data);
        $scenario->spells()->sync($request->validated('spells'));
        $scenario->mobs()->sync($request->validated('mobs'));
        $scenario->npcs()->sync($request->validated('npcs'));
        $scenario->items()->sync($request->validated('items'));
        $scenario->shops()->sync($request->validated('shops'));
        $scenario->ressources()->sync($request->validated('ressources'));
        $scenario->consumables()->sync($request->validated('consumables'));
        $scenario->panoplies()->sync($request->validated('panoplies'));
        $scenario->pages()->sync($request->validated('pages'));

        event(new NotificationSuperAdminEvent('scenario', "update", $scenario, $old_scenario));

        return redirect()->route('scenario.show', ['scenario' => $scenario]);
    }

    public function delete(Scenario $scenario): RedirectResponse
    {
        $this->authorize('delete', $scenario);
        event(new NotificationSuperAdminEvent('scenario', "delete", $scenario));
        $scenario->delete();

        return redirect()->route('scenario.index');
    }

    public function forceDelete(Scenario $scenario): RedirectResponse
    {
        $this->authorize('forceDelete', $scenario);

        $scenario->spells()->detach();
        $scenario->mobs()->detach();
        $scenario->npcs()->detach();
        $scenario->items()->detach();
        $scenario->shops()->detach();
        $scenario->ressources()->detach();
        $scenario->consumables()->detach();
        $scenario->panoplies()->detach();
        $scenario->pages()->detach();

        DataService::deleteFile($scenario, 'image');
        DataService::deleteFile($scenario, 'file');
        event(new NotificationSuperAdminEvent('scenario', "forced_delete", $scenario));
        $scenario->forceDelete();

        return redirect()->route('scenario.index');
    }

    public function restore(Scenario $scenario): RedirectResponse
    {
        $this->authorize('restore', $scenario);

        $scenario->restore();

        return redirect()->route('scenario.index');
    }
}
