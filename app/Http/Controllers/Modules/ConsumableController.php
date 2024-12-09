<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Modules\Consumable;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class ConsumableController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Consumable::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $consumables = Consumable::paginate($paginationMaxDisplay);

        return Inertia::render('consumable.index', [
            'consumables' => $consumables,
        ]);
    }

    public function show(Consumable $consumable, Request $request): \Inertia\Response
    {
        $this->authorize('view', $consumable);

        return Inertia::render('Consumables/Show', [
            'ressources' => $consumable->ressources,
            'panoply' => $consumable->panoply,
            'type' => $consumable->type()
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Consumable::class);

        return Inertia::render('consumable.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Consumable::class);

        $data = DataService::extractData($request, new Consumable(), [
            [
                'disk' => 'modules',
                'path_name' => 'consumables',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $consumable = Consumable::create($data);

        return redirect()->route('consumable.show', ['consumable' => $consumable]);
    }

    public function edit(Consumable $consumable): \Inertia\Response
    {
        $this->authorize('update', $consumable);

        return Inertia::render('consumable.edit', [
            'consumable' => $consumable,
            'ressources' => $consumable->ressources,
            'panoply' => $consumable->panoply,
            'type' => $consumable->type()
        ]);
    }

    public function update(Consumable $consumable, Request $request): RedirectResponse
    {
        $this->authorize('update', $consumable);

        $data = DataService::extractData($request, $consumable(), [
            [
                'disk' => 'modules',
                'path_name' => 'consumables',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $consumable->update($data);

        return redirect()->route('consumable.show', ['consumable' => $consumable]);
    }

    public function delete(Consumable $consumable): RedirectResponse
    {
        $this->authorize('delete', $consumable);

        $consumable->delete();

        return redirect()->route('consumable.index');
    }

    public function forceDelete(Consumable $consumable): RedirectResponse
    {
        $this->authorize('forceDelete', $consumable);

        DataService::deleteFile($consumable, 'image');
        $consumable->forceDelete();

        return redirect()->route('consumable.index');
    }

    public function restore(Consumable $consumable): RedirectResponse
    {
        $this->authorize('restore', $consumable);

        $consumable->restore();

        return redirect()->route('consumable.index');
    }
}
