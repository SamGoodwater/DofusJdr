<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Modules\Campaign;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\DataService;

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

        $data = DataService::extractData($request, new Campaign(), [
            [
                'disk' => 'modules',
                'path_name' => 'campaign',
                'name_bd' => 'image',
                'is_multiple_files' => false, // si true, alors le fichier est un tableau de fichiers
                'compress' => true
            ],
            [
                'disk' => 'modules',
                'path_name' => 'campaign',
                'name_bd' => 'file',
                'is_multiple_files' => true, // si true, alors le fichier est un tableau de fichiers
                'compress' => false
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $campaign = Campaign::create($data);

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

        $data = DataService::extractData($request, new Campaign(), [
            [
                'disk' => 'modules',
                'path_name' => 'campaign',
                'name_bd' => 'image',
                'is_multiple_files' => false, // si true, alors le fichier est un tableau de fichiers
                'compress' => true
            ],
            [
                'disk' => 'modules',
                'path_name' => 'campaign',
                'name_bd' => 'file',
                'is_multiple_files' => true, // si true, alors le fichier est un tableau de fichiers
                'compress' => false
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $campaign->update($data);

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

        $campaign->panoplies()->detach();
        $campaign->users()->detach();
        $campaign->consumables()->detach();
        $campaign->ressources()->detach();
        $campaign->items()->detach();
        $campaign->shops()->detach();
        $campaign->spells()->detach();
        $campaign->npcs()->detach();
        $campaign->mobs()->detach();
        $campaign->scenarios()->detach();
        $campaign->pages()->detach();
        DataService::deleteFile($campaign, 'image', false);
        DataService::deleteFile($campaign, 'file', true);
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
