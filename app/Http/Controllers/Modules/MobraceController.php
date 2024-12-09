<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\MobraceFilterRequest;
use App\Events\NotificationSuperAdminEvent;
use App\Models\Modules\Mobrace;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class MobraceController extends Controller
{
    use AuthorizesRequests;

    public function index(MobraceFilterRequest $request): \Inertia\Response
    {
        $this->authorize('viewAny', Mobrace::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $mobraces = Mobrace::paginate($paginationMaxDisplay);

        return Inertia::render('mobrace.index', [
            'mobraces' => $mobraces,
        ]);
    }

    public function show(Mobrace $mobrace, MobraceFilterRequest $request): \Inertia\Response
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

    public function store(MobraceFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', Mobrace::class);

        $data = DataService::extractData($request, new Mobrace());
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $mobrace = Mobrace::create($data);

        event(new NotificationSuperAdminEvent('mobrace', 'create',  $mobrace));

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

    public function update(Mobrace $mobrace, MobraceFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $mobrace);
        $old_mobrace = clone $mobrace;

        $data = DataService::extractData($request, $mobrace());
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $mobrace->update($data);

        event(new NotificationSuperAdminEvent('mobrace', "update", $mobrace, $old_mobrace));

        return redirect()->route('mobrace.show', ['mobrace' => $mobrace]);
    }

    public function delete(Mobrace $mobrace): RedirectResponse
    {
        $this->authorize('delete', $mobrace);
        event(new NotificationSuperAdminEvent('mobrace', "delete", $mobrace));
        $mobrace->delete();

        return redirect()->route('mobrace.index');
    }

    public function forceDelete(Mobrace $mobrace): RedirectResponse
    {
        $this->authorize('forceDelete', $mobrace);
        event(new NotificationSuperAdminEvent('mobrace', "forced_delete", $mobrace));
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
