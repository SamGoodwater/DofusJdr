<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialization;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class SpecializationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Specialization::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $specializations = Specialization::paginate($paginationMaxDisplay);

        return Inertia::render('specialization.index', [
            'specializations' => $specializations,
        ]);
    }

    public function show(Specialization $specialization, Request $request): \Inertia\Response
    {
        $this->authorize('view', $specialization);

        return Inertia::render('Specializations/Show', [
            'ressources' => $specialization->ressources,
            'panoply' => $specialization->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Specialization::class);

        return Inertia::render('specialization.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Specialization::class);

        $data = $request->validated();
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $data['image'] = $request->file('image')?->store('specializations', 'modules');
        $specialization = Specialization::create($data);

        return redirect()->route('specialization.show', ['specialization' => $specialization]);
    }

    public function edit(Specialization $specialization): \Inertia\Response
    {
        $this->authorize('update', $specialization);

        return Inertia::render('specialization.edit', [
            'specialization' => $specialization,
            'ressources' => $specialization->ressources,
            'panoply' => $specialization->panoply,
        ]);
    }

    public function update(Specialization $specialization, Request $request): RedirectResponse
    {
        $this->authorize('update', $specialization);

        $data = $request->validated();
        $data['image'] = $request->file('image')?->store('specializations', 'modules');
        $specialization->update($data);

        return redirect()->route('specialization.show', ['specialization' => $specialization]);
    }

    public function delete(Specialization $specialization): RedirectResponse
    {
        $this->authorize('delete', $specialization);

        $specialization->delete();

        return redirect()->route('specialization.index');
    }

    public function forceDelete(Specialization $specialization): RedirectResponse
    {
        $this->authorize('forceDelete', $specialization);

        $specialization->forceDelete();

        return redirect()->route('specialization.index');
    }

    public function restore(Specialization $specialization): RedirectResponse
    {
        $this->authorize('restore', $specialization);

        $specialization->restore();

        return redirect()->route('specialization.index');
    }
}
