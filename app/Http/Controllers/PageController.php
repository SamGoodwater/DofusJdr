<?php

namespace App\Http\Controllers;

use App\Http\Requests\PageFilterRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class PageController extends Controller
{
    public function index(PageFilterRequest $request): \Inertia\Response
    {
        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = (int) $request->input('paginationMaxDisplay', 25);

        // Limite la valeur maximale à 500
        if ($paginationMaxDisplay > 500) {
            $paginationMaxDisplay = 500;
        } else if ($paginationMaxDisplay < 1) {
            $paginationMaxDisplay = 1;
        } else {
            $paginationMaxDisplay = (int) $paginationMaxDisplay;
        }

        // $pages = Page::paginate($paginationMaxDisplay);
        $pages = Page::with('page', 'sections')->get();

        return Inertia::render('Pages/Index', [
            'pages' => $pages
        ]);
    }

    public function show(Page $page): \Inertia\Response
    {
        return Inertia::render('Pages/Show', [
            'page' => $page
        ]);
    }

    public function create(): \Inertia\Response
    {
        $page = new Page();
        return Inertia::render('Pages/Create', [
            'page' => $page,
            'pages' => Page::select("uniqid", "name", "is_editable", "public", "is_dropdown")->get()
        ]);
    }

    public function store(PageFilterRequest $request): RedirectResponse
    {
        $page = Page::create($request->validated());

        return redirect()->route('pages.show', ['page' => $page])->with('success', 'La page a bien été créée');
    }

    public function edit(Page $page): \Inertia\Response
    {
        return Inertia::render('Pages/Edit', [
            'page' => $page,
            'pages' => Page::select("uniqid", "name", "is_editable", "public", "is_dropdown")->get()
        ]);
    }

    public function update(Page $page, PageFilterRequest $request): RedirectResponse
    {
        $page->update($request->validated());

        return redirect()->route('pages.show', ['page' => $page])->with('success', 'La page a bien été modifiée');
    }

    public function delete(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('pages.index')->with('success', 'La page a bien été supprimée');
    }
}
