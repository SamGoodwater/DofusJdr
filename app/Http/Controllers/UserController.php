<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', User::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $users = User::paginate($paginationMaxDisplay);

        return Inertia::render('user.index', [
            'users' => $users,
        ]);
    }

    public function show(User $user, Request $request): \Inertia\Response
    {
        $this->authorize('view', $user);

        return Inertia::render('Users/Show', [
            'ressources' => $user->ressources,
            'panoply' => $user->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('user.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = DataService::extractData($request, new User, [
            [
                'disk' => 'modules',
                'path_name' => 'users',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $user = User::create($data);

        return redirect()->route('user.show', ['user' => $user]);
    }

    public function edit(User $user): \Inertia\Response
    {
        $this->authorize('update', $user);

        return Inertia::render('user.edit', [
            'user' => $user,
            'ressources' => $user->ressources,
            'panoply' => $user->panoply,
        ]);
    }

    public function update(User $user, Request $request): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = DataService::extractData($request, $user, [
            [
                'disk' => 'modules',
                'path_name' => 'users',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $user->update($data);

        return redirect()->route('user.show', ['user' => $user]);
    }

    public function delete(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('user.index');
    }

    public function forceDelete(User $user): RedirectResponse
    {
        $this->authorize('forceDelete', $user);

        DataService::deleteFile($user, 'image');
        $user->forceDelete();

        return redirect()->route('user.index');
    }

    public function restore(User $user): RedirectResponse
    {
        $this->authorize('restore', $user);

        $user->restore();

        return redirect()->route('user.index');
    }
}
