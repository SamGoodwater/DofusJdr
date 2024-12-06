<?php

namespace App\Http\Controllers;

use App\Http\Requests\PageFilterRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    use AuthorizesRequests;

    public function index(PageFilterRequest $request): \Inertia\Response
    {
        $this->authorize('viewAny', Page::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));


        // Il faut orderBy les sections par order_num
        $pages = Page::with('page', 'sections')->orderBy('order_num')->paginate($paginationMaxDisplay);

        return inertia('Pages/index', [
            'pages' => $pages
        ]);
    }

    public function show(Page $page): \Inertia\Response
    {
        $this->authorize('view', $page);

        return inertia('Pages/Show', [
            'page' => $page,
            "sections" => $page->sections()->orderBy("order_num")->get(),
            "campaigns" => $page->campaigns()->get(),
            "scenarios" => $page->scenarios()->get(),
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Page::class);

        $page = new Page();
        return inertia('Pages/Create', [
            'page' => $page,
            'pages' => Page::orderBy('order_num')->pluck("name", "is_editable", "is_public", "is_visible", "is_dropdown", "uniqid",)
        ]);
    }

    public function store(PageFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', Page::class);

        $data = $request->validated();
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $page = Page::create($data);
        $page->sections()?->sync($request->validated('sections'));
        $page->specialization()?->sync($request->validated('specialization'));
        $page->campaigns()?->sync($request->validated('campaigns'));
        $page->scenarios()?->sync($request->validated('scenarios'));

        return redirect()->route('pages.show', ['page' => $page])->with('success', 'La page a bien été créée');
    }

    public function edit(Page $page): \Inertia\Response
    {
        $this->authorize('update', $page);

        return inertia('Pages/Edit', [
            'page' => $page,
            'pages' => Page::orderBy('order_num')->pluck("name", "is_editable", "is_public", "is_visible", "is_dropdown", "uniqid",)
        ]);
    }

    public function update(Page $page, PageFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $page);

        $page->update($request->validated());
        $page->sections()?->sync($request->validated('sections'));
        $page->specialization()?->sync($request->validated('specialization'));
        $page->campaigns()?->sync($request->validated('campaigns'));
        $page->scenarios()?->sync($request->validated('scenarios'));

        return redirect()->route('pages.show', ['page' => $page])->with('success', 'La page a bien été modifiée');
    }

    public function delete(Page $page): RedirectResponse
    {
        $this->authorize('delete', $page);

        $page->delete();

        return redirect()->route('pages.index')->with('success', 'La page a bien été supprimée');
    }

    public function forcedDelete(Page $page): RedirectResponse
    {
        $this->authorize('forceDelete', $page);

        if (!$page->trashed()) {
            return redirect()->route('pages.index')->with('error', 'La page n\'est pas dans la corbeille');
        }
        $page->forceDelete();

        return redirect()->route('pages.index')->with('success', 'La page a bien été supprimée définitivement');
    }

    public function restore(Page $page): RedirectResponse
    {
        $this->authorize('restore', $page);

        if (!$page->trashed()) {
            return redirect()->route('pages.index')->with('error', 'La page n\'est pas dans la corbeille');
        }
        $page->restore();

        return redirect()->route('pages.index')->with('success', 'La page a bien été restaurée');
    }
}
