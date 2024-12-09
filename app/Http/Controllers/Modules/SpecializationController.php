<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\SpecializationFilterRequest;
use App\Events\NotificationSuperAdminEvent;
use App\Models\Modules\Specialization;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class SpecializationController extends Controller
{
    use AuthorizesRequests;

    public function index(SpecializationFilterRequest $request): \Inertia\Response
    {
        $this->authorize('viewAny', Specialization::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $specializations = Specialization::paginate($paginationMaxDisplay);

        return Inertia::render('specialization.index', [
            'specializations' => $specializations,
        ]);
    }

    public function show(Specialization $specialization, SpecializationFilterRequest $request): \Inertia\Response
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

    public function store(SpecializationFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', Specialization::class);

        $data = DataService::extractData($request, new Specialization(), [
            [
                'disk' => 'modules',
                'path_name' => 'specializations',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $specialization = Specialization::create($data);
        $specialization->capabilities()->sync($request->input('capabilities'));
        $specialization->pages()->sync($request->input('pages'));

        event(new NotificationSuperAdminEvent('specialization', 'create',  $specialization));

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

    public function update(Specialization $specialization, SpecializationFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $specialization);
        $old_specialization = $specialization;

        $data = DataService::extractData($request, $specialization, [
            [
                'disk' => 'modules',
                'path_name' => 'specializations',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $specialization->update($data);
        $specialization->capabilities()->sync($request->input('capabilities'));
        $specialization->pages()->sync($request->input('pages'));

        event(new NotificationSuperAdminEvent('specialization', "update", $specialization, $old_specialization));

        return redirect()->route('specialization.show', ['specialization' => $specialization]);
    }

    public function delete(Specialization $specialization): RedirectResponse
    {
        $this->authorize('delete', $specialization);
        event(new NotificationSuperAdminEvent('specialization', "delete", $specialization));
        $specialization->delete();

        return redirect()->route('specialization.index');
    }

    public function forceDelete(Specialization $specialization): RedirectResponse
    {
        $this->authorize('forceDelete', $specialization);

        $specialization->capabilities()->detach();
        $specialization->pages()->detach();

        DataService::deleteFile($specialization, 'image');
        event(new NotificationSuperAdminEvent('specialization', "forced_delete", $specialization));
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
