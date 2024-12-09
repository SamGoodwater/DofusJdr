<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectionFilterRequest;
use App\Models\Section;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\DataService;

class SectionController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Section::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $sections = Section::orderBy("order_num")->with('page')->paginate($paginationMaxDisplay);

        return Inertia::render('section.index', [
            'section' => $sections,
        ]);
    }

    public function show(Section $section): \Inertia\Response
    {
        $this->authorize('view', $section);

        return Inertia::render('Sections/Show', [
            'section' => $section,
            'files' => $section->getPathFiles()
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Section::class);

        $section = new Section();
        return Inertia::render('Sections/Create', [
            'section' => $section,
            'pages' => Page::orderBy('order_num')->pluck("name", "is_editable", "is_public", "is_visible", "is_dropdown", "uniqid",)
        ]);
    }

    public function store(SectionFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', Section::class);

        $data = DataService::extractData($request, new Section(), [
            [
                'disk' => 'modules',
                'path_name' => 'sections',
                'name_bd' => 'file',
                'is_multiple_files' => true, // si true, alors le fichier est un tableau de fichiers
                'compress' => false
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $section = Section::create($data);

        return redirect()->route('sections.show', ['section' => $section])->with('success', 'La section a bien été créée');
    }

    public function edit(Section $section): \Inertia\Response
    {
        $this->authorize('update', $section);

        return Inertia::render('Sections/Edit', [
            'section' => $section,
            'pages' => Page::pluck("name", "is_editable", "is_public", "is_visible", "is_dropdown", "uniqid",),
            'files' => $section->getPathFiles()
        ]);
    }

    public function update(Section $section, SectionFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $section);

        $data = DataService::extractData($request, $section, [
            [
                'disk' => 'modules',
                'path_name' => 'sections',
                'name_bd' => 'file',
                'is_multiple_files' => true, // si true, alors le fichier est un tableau de fichiers
                'compress' => false
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $section->update($data);

        return redirect()->route('sections.show', ['section' => $section])->with('success', 'La section a bien été modifiée');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $this->authorize('delete', $section);

        $section->delete();

        return redirect()->route('sections.index')->with('success', 'La section a bien été supprimée');
    }

    public function forcedDelete(Section $section): RedirectResponse
    {
        $this->authorize('forceDelete', $section);

        DataService::deleteFile($section, 'file');
        $section->forceDelete();

        return redirect()->route('sections.index')->with('success', 'La section a bien été supprimée définitivement');
    }

    public function restore(Section $section): RedirectResponse
    {
        $this->authorize('restore', $section);

        if (!$section->trashed()) {
            return redirect()->route('sections.index')->with('error', 'La section n\'est pas dans la corbeille');
        }
        $section->restore();

        return redirect()->route('sections.index')->with('success', 'La section a bien été restaurée');
    }
}
