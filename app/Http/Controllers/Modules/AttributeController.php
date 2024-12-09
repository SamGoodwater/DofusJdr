<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Modules\Attribute;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;


class AttributeController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Attribute::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $attributes = Attribute::paginate($paginationMaxDisplay);

        return Inertia::render('attribute.index', [
            'attributes' => $attributes,
        ]);
    }

    public function show(Attribute $attribute, Request $request): \Inertia\Response
    {
        $this->authorize('view', $attribute);

        return Inertia::render('Attributes/Show', [
            'ressources' => $attribute->ressources,
            'panoply' => $attribute->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Attribute::class);

        return Inertia::render('attribute.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Attribute::class);

        $data = DataService::extractData($request, new Attribute(), [
            [
                'disk' => 'modules',
                'path_name' => 'attributes',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $attribute = Attribute::create($data);

        return redirect()->route('attribute.show', ['attribute' => $attribute]);
    }

    public function edit(Attribute $attribute): \Inertia\Response
    {
        $this->authorize('update', $attribute);

        return Inertia::render('attribute.edit', [
            'attribute' => $attribute,
            'ressources' => $attribute->ressources,
            'panoply' => $attribute->panoply,
        ]);
    }

    public function update(Attribute $attribute, Request $request): RedirectResponse
    {
        $this->authorize('update', $attribute);

        $data = DataService::extractData($request, $attribute, [
            [
                'disk' => 'modules',
                'path_name' => 'attributes',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $attribute->update($data);

        return redirect()->route('attribute.show', ['attribute' => $attribute]);
    }

    public function delete(Attribute $attribute): RedirectResponse
    {
        $this->authorize('delete', $attribute);

        $attribute->delete();

        return redirect()->route('attribute.index');
    }

    public function forceDelete(Attribute $attribute): RedirectResponse
    {
        $this->authorize('forceDelete', $attribute);
        DataService::deleteFile($attribute, 'image');
        $attribute->forceDelete();

        return redirect()->route('attribute.index');
    }

    public function restore(Attribute $attribute): RedirectResponse
    {
        $this->authorize('restore', $attribute);

        $attribute->restore();

        return redirect()->route('attribute.index');
    }
}
