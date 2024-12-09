<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Modules\Ressource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class RessourceController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Ressource::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $ressources = Ressource::paginate($paginationMaxDisplay);

        return Inertia::render('ressource.index', [
            'ressources' => $ressources,
        ]);
    }

    public function show(Ressource $ressource, Request $request): \Inertia\Response
    {
        $this->authorize('view', $ressource);

        return Inertia::render('Ressources/Show', [
            'ressources' => $ressource->ressources,
            'panoply' => $ressource->panoply,
            'type' => $ressource->type()
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Ressource::class);

        return Inertia::render('ressource.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Ressource::class);

        $data = DataService::extractData($request, new Ressource(), [
            [
                'disk' => 'modules',
                'path_name' => 'ressources',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $ressource = Ressource::create($data);

        return redirect()->route('ressource.show', ['ressource' => $ressource]);
    }

    public function edit(Ressource $ressource): \Inertia\Response
    {
        $this->authorize('update', $ressource);

        return Inertia::render('ressource.edit', [
            'ressource' => $ressource,
            'ressources' => $ressource->ressources,
            'panoply' => $ressource->panoply,
            'type' => $ressource->type()
        ]);
    }

    public function update(Ressource $ressource, Request $request): RedirectResponse
    {
        $this->authorize('update', $ressource);

        $data = DataService::extractData($request, new Ressource(), [
            [
                'disk' => 'modules',
                'path_name' => 'ressources',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $ressource->update($data);

        return redirect()->route('ressource.show', ['ressource' => $ressource]);
    }

    public function delete(Ressource $ressource): RedirectResponse
    {
        $this->authorize('delete', $ressource);

        $ressource->delete();

        return redirect()->route('ressource.index');
    }

    public function forceDelete(Ressource $ressource): RedirectResponse
    {
        $this->authorize('forceDelete', $ressource);

        DataService::deleteFile($ressource, 'image');
        $ressource->forceDelete();

        return redirect()->route('ressource.index');
    }

    public function restore(Ressource $ressource): RedirectResponse
    {
        $this->authorize('restore', $ressource);

        $ressource->restore();

        return redirect()->route('ressource.index');
    }
}
