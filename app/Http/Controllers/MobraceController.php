<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mobrace;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class MobraceController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Mobrace::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $mobraces = Mobrace::paginate($paginationMaxDisplay);

        return Inertia::render('mobrace.index', [
            'mobraces' => $mobraces,
        ]);
    }

    public function show(Mobrace $mobrace, Request $request): \Inertia\Response
    {
        $this->authorize('view', $mobrace);

        return Inertia::render('Mobraces/Show', [
            'ressources' => $mobrace->ressources,
            'panoply' => $mobrace->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Mobrace::class);

        return Inertia::render('mobrace.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Mobrace::class);

        $data = $request->validated();
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $data['image'] = $request->file('image')?->store('mobraces', 'modules');
        $mobrace = Mobrace::create($data);

        return redirect()->route('mobrace.show', ['mobrace' => $mobrace]);
    }

    public function edit(Mobrace $mobrace): \Inertia\Response
    {
        $this->authorize('update', $mobrace);

        return Inertia::render('mobrace.edit', [
            'mobrace' => $mobrace,
            'ressources' => $mobrace->ressources,
            'panoply' => $mobrace->panoply,
        ]);
    }

    public function update(Mobrace $mobrace, Request $request): RedirectResponse
    {
        $this->authorize('update', $mobrace);

        $data = $request->validated();
        $data['image'] = $request->file('image')?->store('mobraces', 'modules');
        $mobrace->update($data);

        return redirect()->route('mobrace.show', ['mobrace' => $mobrace]);
    }

    public function delete(Mobrace $mobrace): RedirectResponse
    {
        $this->authorize('delete', $mobrace);

        $mobrace->delete();

        return redirect()->route('mobrace.index');
    }

    public function forceDelete(Mobrace $mobrace): RedirectResponse
    {
        $this->authorize('forceDelete', $mobrace);

        $mobrace->forceDelete();

        return redirect()->route('mobrace.index');
    }

    public function restore(Mobrace $mobrace): RedirectResponse
    {
        $this->authorize('restore', $mobrace);

        $mobrace->restore();

        return redirect()->route('mobrace.index');
    }
}
