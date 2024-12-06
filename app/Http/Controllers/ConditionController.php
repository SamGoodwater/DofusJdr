<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Condition;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class ConditionController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Condition::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $conditions = Condition::paginate($paginationMaxDisplay);

        return Inertia::render('condition.index', [
            'conditions' => $conditions,
        ]);
    }

    public function show(Condition $condition, Request $request): \Inertia\Response
    {
        $this->authorize('view', $condition);

        return Inertia::render('Conditions/Show', [
            'ressources' => $condition->ressources,
            'panoply' => $condition->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Condition::class);

        return Inertia::render('condition.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Condition::class);

        $data = $request->validated();
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $data['image'] = $request->file('image')?->store('conditions', 'modules');
        $condition = Condition::create($data);

        return redirect()->route('condition.show', ['condition' => $condition]);
    }

    public function edit(Condition $condition): \Inertia\Response
    {
        $this->authorize('update', $condition);

        return Inertia::render('condition.edit', [
            'condition' => $condition,
            'ressources' => $condition->ressources,
            'panoply' => $condition->panoply,
        ]);
    }

    public function update(Condition $condition, Request $request): RedirectResponse
    {
        $this->authorize('update', $condition);

        $data = $request->validated();
        $data['image'] = $request->file('image')?->store('conditions', 'modules');
        $condition->update($data);

        return redirect()->route('condition.show', ['condition' => $condition]);
    }

    public function delete(Condition $condition): RedirectResponse
    {
        $this->authorize('delete', $condition);

        $condition->delete();

        return redirect()->route('condition.index');
    }

    public function forceDelete(Condition $condition): RedirectResponse
    {
        $this->authorize('forceDelete', $condition);

        $condition->forceDelete();

        return redirect()->route('condition.index');
    }

    public function restore(Condition $condition): RedirectResponse
    {
        $this->authorize('restore', $condition);

        $condition->restore();

        return redirect()->route('condition.index');
    }
}
