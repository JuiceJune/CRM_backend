<?php

namespace App\Http\Controllers\Admin\Campaign;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Campaign\CampaignStoreRequest;
use App\Http\Requests\Admin\Campaign\CampaignUpdateRequest;
use App\Models\Mailbox;
use App\Models\Campaign;
use App\Models\Project;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campaigns = Campaign::all();
        return view('admin.campaign.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mailboxes = Mailbox::all();
        $projects = Project::all();
        return view('admin.campaign.create',
            compact('mailboxes', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CampaignStoreRequest $request)
    {
        $validated = $request->validated();

        $campaign = Campaign::create($validated);

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $campaign = Campaign::findOrFail($id);
        return view('admin.campaign.show', compact('campaign'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $campaign = Campaign::findOrFail($id);
        $mailboxes = Mailbox::all();
        $projects = Project::all();
        return view('admin.campaign.edit', compact('campaign', 'mailboxes', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CampaignUpdateRequest $request, string $id)
    {
        $validated = $request->validated();

        $campaign = Campaign::findOrFail($id);

        $campaign->update($validated);

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //TODO delete avatar file
        $campaign = Campaign::find($id);
        $campaign->prospects()->delete();
        if ($campaign->delete()) {
            return redirect()->route('admin.campaigns.index')->with('success', 'Campaign deleted successfully.');
        } else {
            return redirect()->route('admin.campaigns.index')->with('error', 'Campaign not deleted.');
        }
    }
}
