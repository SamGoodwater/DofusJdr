<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Modules\Capability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;
use Inertia\Inertia;

class CapabilityController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Capability::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $capabilitys = Capability::paginate($paginationMaxDisplay);

        return Inertia::render('capability.index', [
            'capabilitys' => $capabilitys,
        ]);
    }

    public function show(Capability $capability, Request $request): \Inertia\Response
    {
        $this->authorize('view', $capability);

        return Inertia::render('Capabilitys/Show', [
            'ressources' => $capability->ressources,
            'panoply' => $capability->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Capability::class);

        return Inertia::render('capability.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Capability::class);

        $data = DataService::extractData($request, new Capability(), [
            [
                'disk' => 'modules',
                'path_name' => 'capabilities',
                'name_bd' => 'image',
                'is_multiple_files' => false, // si true, alors le fichier est un tableau de fichiers
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";

        $capability = Capability::create($data);

        return redirect()->route('capability.show', ['capability' => $capability]);
    }

    public function edit(Capability $capability): \Inertia\Response
    {
        $this->authorize('update', $capability);

        return Inertia::render('capability.edit', [
            'capability' => $capability,
            'ressources' => $capability->ressources,
            'panoply' => $capability->panoply,
        ]);
    }

    public function update(Capability $capability, Request $request): RedirectResponse
    {
        $this->authorize('update', $capability);

        $data = DataService::extractData($request, $capability, [
            [
                'disk' => 'modules',
                'path_name' => 'capabilities',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $capability->update($data);

        return redirect()->route('capability.show', ['capability' => $capability]);
    }

    public function delete(Capability $capability): RedirectResponse
    {
        $this->authorize('delete', $capability);

        $capability->delete();

        return redirect()->route('capability.index');
    }

    public function forceDelete(Capability $capability): RedirectResponse
    {
        $this->authorize('forceDelete', $capability);

        DataService::deleteFile($capability, 'image');
        $capability->forceDelete();

        return redirect()->route('capability.index');
    }

    public function restore(Capability $capability): RedirectResponse
    {
        $this->authorize('restore', $capability);

        $capability->restore();

        return redirect()->route('capability.index');
    }
}
