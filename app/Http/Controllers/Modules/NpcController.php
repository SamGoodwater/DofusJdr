<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Modules\Npc;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class NpcController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Npc::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $npcs = Npc::paginate($paginationMaxDisplay);

        return Inertia::render('npc.index', [
            'npcs' => $npcs,
        ]);
    }

    public function show(Npc $npc, Request $request): \Inertia\Response
    {
        $this->authorize('view', $npc);

        return Inertia::render('Npcs/Show', [
            'ressources' => $npc->ressources,
            'panoply' => $npc->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Npc::class);

        return Inertia::render('npc.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Npc::class);

        $data = DataService::extractData($request, new Npc(), [
            [
                'disk' => 'modules',
                'path_name' => 'npcs',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $npc = Npc::create($data);

        return redirect()->route('npc.show', ['npc' => $npc]);
    }

    public function edit(Npc $npc): \Inertia\Response
    {
        $this->authorize('update', $npc);

        return Inertia::render('npc.edit', [
            'npc' => $npc,
            'ressources' => $npc->ressources,
            'panoply' => $npc->panoply,
        ]);
    }

    public function update(Npc $npc, Request $request): RedirectResponse
    {
        $this->authorize('update', $npc);

        $data = DataService::extractData($request, new Npc(), [
            [
                'disk' => 'modules',
                'path_name' => 'npcs',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $npc->update($data);

        return redirect()->route('npc.show', ['npc' => $npc]);
    }

    public function delete(Npc $npc): RedirectResponse
    {
        $this->authorize('delete', $npc);

        $npc->delete();

        return redirect()->route('npc.index');
    }

    public function forceDelete(Npc $npc): RedirectResponse
    {
        $this->authorize('forceDelete', $npc);

        DataService::deleteFile($npc, 'image');
        $npc->forceDelete();

        return redirect()->route('npc.index');
    }

    public function restore(Npc $npc): RedirectResponse
    {
        $this->authorize('restore', $npc);

        $npc->restore();

        return redirect()->route('npc.index');
    }
}
