<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Modules\Classe;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class ClasseController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Classe::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $classes = Classe::paginate($paginationMaxDisplay);

        return Inertia::render('classe.index', [
            'classes' => $classes,
        ]);
    }

    public function show(Classe $classe, Request $request): \Inertia\Response
    {
        $this->authorize('view', $classe);

        return Inertia::render('Classes/Show', [
            'ressources' => $classe->ressources,
            'panoply' => $classe->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Classe::class);

        return Inertia::render('classe.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Classe::class);

        $data = DataService::extractData($request, new Classe(), [
            [
                'disk' => 'modules',
                'path_name' => 'classes',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ],
            [
                'disk' => 'modules',
                'path_name' => 'classes',
                'name_bd' => 'icon',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $classe = Classe::create($data);

        return redirect()->route('classe.show', ['classe' => $classe]);
    }

    public function edit(Classe $classe): \Inertia\Response
    {
        $this->authorize('update', $classe);

        return Inertia::render('classe.edit', [
            'classe' => $classe,
            'ressources' => $classe->ressources,
            'panoply' => $classe->panoply,
        ]);
    }

    public function update(Classe $classe, Request $request): RedirectResponse
    {
        $this->authorize('update', $classe);

        $data = DataService::extractData($request, $classe, [
            [
                'disk' => 'modules',
                'path_name' => 'classes',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ],
            [
                'disk' => 'modules',
                'path_name' => 'classes',
                'name_bd' => 'icon',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $classe->update($data);

        return redirect()->route('classe.show', ['classe' => $classe]);
    }

    public function delete(Classe $classe): RedirectResponse
    {
        $this->authorize('delete', $classe);

        $classe->delete();

        return redirect()->route('classe.index');
    }

    public function forceDelete(Classe $classe): RedirectResponse
    {
        $this->authorize('forceDelete', $classe);

        DataService::deleteFile($classe, 'image');
        DataService::deleteFile($classe, 'icon');
        $classe->forceDelete();

        return redirect()->route('classe.index');
    }

    public function restore(Classe $classe): RedirectResponse
    {
        $this->authorize('restore', $classe);

        $classe->restore();

        return redirect()->route('classe.index');
    }
}
