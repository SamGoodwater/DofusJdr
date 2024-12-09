<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Modules\Scenario;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class ScenarioController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Scenario::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $scenarios = Scenario::paginate($paginationMaxDisplay);

        return Inertia::render('scenario.index', [
            'scenarios' => $scenarios,
        ]);
    }

    public function show(Scenario $scenario, Request $request): \Inertia\Response
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

    public function store(Request $request): RedirectResponse
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

    public function update(Scenario $scenario, Request $request): RedirectResponse
    {
        $this->authorize('update', $scenario);

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

        return redirect()->route('scenario.show', ['scenario' => $scenario]);
    }

    public function delete(Scenario $scenario): RedirectResponse
    {
        $this->authorize('delete', $scenario);

        $scenario->delete();

        return redirect()->route('scenario.index');
    }

    public function forceDelete(Scenario $scenario): RedirectResponse
    {
        $this->authorize('forceDelete', $scenario);

        DataService::deleteFile($scenario, 'image');
        DataService::deleteFile($scenario, 'file');
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
