<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SectionController extends Controller
{
    use AuthorizesRequests;

    public function index(): \Inertia\Response
    {
        $this->authorize('viewAny', Section::class);

        $sections = Section::orderBy("order_num")->with('page')->get();

        return Inertia::render('Sections/Index', [
            'sections' => $sections
        ]);
    }

    public function show(Section $section): \Inertia\Response
    {
        $this->authorize('view', $section);

        return Inertia::render('Sections/Show', [
            'section' => $section
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Section::class);

        $section = new Section();
        return Inertia::render('Sections/Create', [
            'section' => $section,
            'pages' => Page::pluck("name", "is_editable", "is_public", "is_visible", "is_dropdown", "uniqid",)
        ]);
    }

    public function store(): RedirectResponse
    {
        $this->authorize('create', Section::class);

        $section = Section::create(request()->all());

        return redirect()->route('sections.show', ['section' => $section])->with('success', 'La section a bien été créée');
    }

    public function edit(Section $section): \Inertia\Response
    {
        $this->authorize('update', $section);

        return Inertia::render('Sections/Edit', [
            'section' => $section,
            'pages' => Page::pluck("name", "is_editable", "is_public", "is_visible", "is_dropdown", "uniqid",)
        ]);
    }

    public function update(Section $section): RedirectResponse
    {
        $this->authorize('update', $section);

        $section->update(request()->all());

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

        if (!$section->trashed()) {
            return redirect()->route('sections.index')->with('error', 'La section n\'est pas dans la corbeille');
        }
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
