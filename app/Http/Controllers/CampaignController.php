<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Campaign::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $campaigns = Campaign::paginate($paginationMaxDisplay);

        return Inertia::render('campaign.index', [
            'campaigns' => $campaigns,
        ]);
    }

    public function show(Campaign $campaign, Request $request): \Inertia\Response
    {
        $this->authorize('view', $campaign);

        return Inertia::render('Campaigns/Show', [
            'ressources' => $campaign->ressources,
            'files' => $campaign->getPathFiles(),
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Campaign::class);

        return Inertia::render('campaign.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Campaign::class);

        $data = $request->validated();
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $data['image'] = $request->file('image')?->store('campaigns', 'modules');
        $campaign = Campaign::create($data);
        $file = $request->validated('file') ?? null;
        $campaign->setPathFiles($file?->store('campaigns', 'modules'));

        return redirect()->route('campaign.show', ['campaign' => $campaign]);
    }

    public function edit(Campaign $campaign): \Inertia\Response
    {
        $this->authorize('update', $campaign);

        return Inertia::render('campaign.edit', [
            'campaign' => $campaign,
            'ressources' => $campaign->ressources,
            'files' => $campaign->getPathFiles(),
        ]);
    }

    public function update(Campaign $campaign, Request $request): RedirectResponse
    {
        $this->authorize('update', $campaign);

        $data = $request->validated();
        $data['image'] = $request->file('image')?->store('campaigns', 'modules');
        $campaign->update($data);
        $file = $request->validated('file') ?? null;
        $campaign->setPathFiles($file?->store('campaigns', 'modules'));

        return redirect()->route('campaign.show', ['campaign' => $campaign]);
    }

    public function delete(Campaign $campaign): RedirectResponse
    {
        $this->authorize('delete', $campaign);

        $campaign->delete();

        return redirect()->route('campaign.index');
    }

    public function forceDelete(Campaign $campaign): RedirectResponse
    {
        $this->authorize('forceDelete', $campaign);

        $campaign->forceDelete();

        return redirect()->route('campaign.index');
    }

    public function restore(Campaign $campaign): RedirectResponse
    {
        $this->authorize('restore', $campaign);

        $campaign->restore();

        return redirect()->route('campaign.index');
    }
}
