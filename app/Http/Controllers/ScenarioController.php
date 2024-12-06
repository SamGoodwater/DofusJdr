<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scenario;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

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

        $data = $request->validated();
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $data['image'] = $request->file('image')?->store('campaigns', 'modules');
        $scenario = Scenario::create($data);
        $file = $request->validated('file') ?? null;
        $scenario->setPathFiles($file?->store('scenarios', 'modules'));

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

        $data = $request->validated();
        $data['image'] = $request->file('image')?->store('scenarios', 'modules');
        $scenario->update($data);
        $file = $request->validated('file') ?? null;
        $scenario->setPathFiles($file?->store('scenarios', 'modules'));

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
