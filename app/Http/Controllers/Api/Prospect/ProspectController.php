<?php

namespace App\Http\Controllers\Api\Prospect;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Prospect\ProspectsStoreRequest;
use App\Http\Requests\Admin\Prospect\ProspectStoreRequest;
use App\Http\Requests\Admin\Prospect\ProspectUpdateRequest;
use App\Http\Resources\ProspectResource;
use App\Models\Campaign;
use App\Models\CampaignStepProspect;
use App\Models\Prospect;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PHPUnit\Exception;
use PHPUnit\Framework\Error;

class ProspectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $campaign_id = $request->input('campaign_id');
            $limit = $request->input('limit', 100);
            $offset = $request->input('offset', 0);

            $query = $campaign_id
                ? Campaign::find($campaign_id)->prospects()->skip($offset)->take($limit)
                : Prospect::skip($offset)->take($limit);

            $prospects = $query->get();

            return response(ProspectResource::collection($prospects));
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
//        try {
//            $project_id = $request->input('project_id');
//            if($project_id) {
//                $projects = Project::find($project_id);
//                $mailboxes = $projects->mailboxes;
//                return response(["mailboxes" => $mailboxes]);
//            } else {
//                $mailboxes = Mailbox::select('id', 'email', 'name')->get();
//                $projects = Project::select('id', 'name')->get();
//                return response(["mailboxes" => $mailboxes, "projects" => $projects]);
//            }
//        } catch (Exception $error) {
//            return response($error, 400);
//        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProspectsStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $prospects = $validated['prospects'];
            $campaignId = $validated['campaign_id'];

            $campaign = Campaign::find($campaignId);
            $firstStep = $campaign->step(1);

            if (!$campaign) {
                throw new Error('Campaign not found');
            }

            $timezone = $campaign->timezone;
            $dateInTimeZone = Carbon::now($timezone);

            foreach ($prospects as $prospect) {
                $createdProspect = Prospect::create($prospect);
                $campaign->prospects()->attach($createdProspect->id);

                $campaignStepProspect = CampaignStepProspect::create([
                    'campaign_id' => $campaign->id,
                    'campaign_step_id' => $firstStep->id,
                    'prospect_id' => $createdProspect->id,
                    'available_at' => $dateInTimeZone,
                ]);
            }

            return response([
                "message" => "Prospects were successfully created",
            ], 200);
        } catch (Exception $error) {
            return response([
                "message" => "Problem with store Prospects",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Prospect $campaign)
    {
        try {
            $currentProspect = new ProspectResource($campaign);
            return response()->json($currentProspect);
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prospect $campaign)
    {
//        try {
//            $mailboxes = Mailbox::select('id', 'email', 'name')->get();
//            $projects = Project::select('id', 'name')->get();
//            return response(["campaign" => $campaign, "mailboxes" => $mailboxes, "projects" => $projects]);
//        } catch (Exception $error) {
//            return response($error, 400);
//        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProspectUpdateRequest $request, Prospect $prospect)
    {
        try {
            $validated = $request->validated();
            $prospect->update($validated);
            return response($prospect);
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $prospect = Prospect::find($id);
            $prospect->delete();
            return response('Prospect deleted successfully');
        } catch (Exception $error) {
            return response([
                "message" => "Problem with deleting prospect",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    public function csvUpload(Request $request)
    {
        try {
            $campaignId = $request->input('campaign_id');
            $campaign = Campaign::find($campaignId);

            if (!$campaign) {
                throw new Error('Campaign not found');
            }

            $firstStep = $campaign->step(1);

            $timezone = $campaign->timezone;
            $dateInTimeZone = Carbon::now($timezone);

            if ($request->hasFile('csv_file')) {
                $file = $request->file('csv_file');

                if ($file->getClientOriginalExtension() === 'csv') {
                    $filePath = $file->getRealPath();
                    $handle = fopen($filePath, 'r');

                    if ($handle !== false) {
                        fgetcsv($handle);

                        while (($data = fgetcsv($handle)) !== false) {
                           $prospect = new Prospect([
                                    'first_name' => $data[0],
                                    'last_name' => $data[1],
                                    'email' => $data[2],
                                ]);
                                $prospect->save();

                            $campaign->prospects()->attach($prospect->id);

                            $campaignStepProspect = CampaignStepProspect::create([
                                'campaign_id' => $campaign->id,
                                'campaign_step_id' => $firstStep->id,
                                'prospect_id' => $prospect->id,
                                'available_at' => $dateInTimeZone,
                            ]);
                        }

                        fclose($handle);
                    }

                    return response('Prospects uploaded successfully');
                } else {
                    return response([
                        "message" => "Error",
                        "error_message" => 'Invalid file format. Please upload a CSV file',
                    ], 400);
                }
            } else {
                return response([
                    "message" => "Error",
                    "error_message" => 'No file uploaded',
                ], 400);
            }
        } catch (Exception $error) {
            return response([
                "message" => "Problem with csv prospects upload",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }
}
