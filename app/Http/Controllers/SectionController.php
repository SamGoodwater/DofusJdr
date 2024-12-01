<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class SectionController extends Controller
{
    public function index(): \Inertia\Response
    {
        $sections = Section::orderBy("order_num")->with('page')->get();

        return Inertia::render('Sections/Index', [
            'sections' => $sections
        ]);
    }

    public function show(Section $section): \Inertia\Response
    {
        return Inertia::render('Sections/Show', [
            'section' => $section
        ]);
    }

    public function create(): \Inertia\Response
    {
        $section = new Section();
        return Inertia::render('Sections/Create', [
            'section' => $section,
            'pages' => Page::pluck("name", "is_editable", "public", "is_dropdown", "uniqid",)
        ]);
    }

    public function store(): RedirectResponse
    {
        $section = Section::create(request()->all());

        return redirect()->route('sections.show', ['section' => $section])->with('success', 'La section a bien été créée');
    }

    public function edit(Section $section): \Inertia\Response
    {
        return Inertia::render('Sections/Edit', [
            'section' => $section,
            'pages' => Page::pluck("name", "is_editable", "public", "is_dropdown", "uniqid",)
        ]);
    }

    public function update(Section $section): RedirectResponse
    {
        $section->update(request()->all());

        return redirect()->route('sections.show', ['section' => $section])->with('success', 'La section a bien été modifiée');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $section->delete();

        return redirect()->route('sections.index')->with('success', 'La section a bien été supprimée');
    }

    public function forcedDelete(Section $section): RedirectResponse
    {
        if (!$section->trashed()) {
            return redirect()->route('sections.index')->with('error', 'La section n\'est pas dans la corbeille');
        }
        $section->forceDelete();

        return redirect()->route('sections.index')->with('success', 'La section a bien été supprimée définitivement');
    }

    public function restore(Section $section): RedirectResponse
    {
        if (!$section->trashed()) {
            return redirect()->route('sections.index')->with('error', 'La section n\'est pas dans la corbeille');
        }
        $section->restore();

        return redirect()->route('sections.index')->with('success', 'La section a bien été restaurée');
    }
}
