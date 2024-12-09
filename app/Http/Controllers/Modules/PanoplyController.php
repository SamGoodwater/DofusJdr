<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\PanoplyFilterRequest;
use App\Events\NotificationSuperAdminEvent;
use App\Models\Modules\Panoply;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class PanoplyController extends Controller
{
    use AuthorizesRequests;

    public function index(PanoplyFilterRequest $request): \Inertia\Response
    {
        $this->authorize('viewAny', Panoply::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $panoplys = Panoply::paginate($paginationMaxDisplay);

        return Inertia::render('panoply.index', [
            'panoplys' => $panoplys,
        ]);
    }

    public function show(Panoply $panoply, PanoplyFilterRequest $request): \Inertia\Response
    {
        $this->authorize('view', $panoply);

        return Inertia::render('Panoplys/Show', [
            'ressources' => $panoply->ressources,
            'panoply' => $panoply->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Panoply::class);

        return Inertia::render('panoply.create');
    }

    public function store(PanoplyFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', Panoply::class);

        $data = DataService::extractData($request, new Panoply(), [
            [
                'disk' => 'modules',
                'path_name' => 'panoplys',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $panoply = Panoply::create($data);
        $panoply->items()->sync($request->input('items'));

        event(new NotificationSuperAdminEvent('panoply', 'create',  $panoply));

        return redirect()->route('panoply.show', ['panoply' => $panoply]);
    }

    public function edit(Panoply $panoply): \Inertia\Response
    {
        $this->authorize('update', $panoply);

        return Inertia::render('panoply.edit', [
            'panoply' => $panoply,
            'ressources' => $panoply->ressources,
            'panoply' => $panoply->panoply,
        ]);
    }

    public function update(Panoply $panoply, PanoplyFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $panoply);
        $old_panoply = $panoply;

        $data = DataService::extractData($request, new Panoply(), [
            [
                'disk' => 'modules',
                'path_name' => 'panoplys',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $panoply->update($data);
        $panoply->items()->sync($request->input('items'));

        event(new NotificationSuperAdminEvent('panoply', "update", $panoply, $old_panoply));

        return redirect()->route('panoply.show', ['panoply' => $panoply]);
    }

    public function delete(Panoply $panoply): RedirectResponse
    {
        $this->authorize('delete', $panoply);
        event(new NotificationSuperAdminEvent('panoply', "delete", $panoply));
        $panoply->delete();

        return redirect()->route('panoply.index');
    }

    public function forceDelete(Panoply $panoply): RedirectResponse
    {
        $this->authorize('forceDelete', $panoply);

        $panoply->items()->detach();

        DataService::deleteFile($panoply, 'image');
        event(new NotificationSuperAdminEvent('panoply', "forced_delete", $panoply));
        $panoply->forceDelete();

        return redirect()->route('panoply.index');
    }

    public function restore(Panoply $panoply): RedirectResponse
    {
        $this->authorize('restore', $panoply);

        $panoply->restore();

        return redirect()->route('panoply.index');
    }
}
