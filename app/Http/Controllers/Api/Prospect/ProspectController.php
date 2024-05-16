<?php

namespace App\Http\Controllers\Api\Prospect;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prospect\ProspectsStoreRequest;
use App\Http\Requests\Prospect\ProspectUpdateRequest;
use App\Http\Resources\Prospect\ProspectCampaignResource;
use App\Http\Resources\Prospect\ProspectResource;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\Prospect;
use Carbon\Carbon;
use Exception;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProspectController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $campaign_id = $request->input('campaign_id');
            $limit = $request->input('limit', 100);
            $offset = $request->input('offset', 0);

            $query = $campaign_id
                ? Campaign::query()->where('uuid', $campaign_id)->firstOrFail()->prospects()
                : Prospect::query();

            $prospects = $query->skip($offset)->take($limit)->get();

            return response()->json(ProspectCampaignResource::collection($prospects));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProspectsStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $user = $request->user();
            $account_id = $user->account_id;

            $prospects = $validated['prospects'];
            $campaignUuid = $validated['campaign_id'];

            $campaign = Campaign::where('uuid', $campaignUuid)->firstOrFail();

            $firstStep = $campaign->step(1);
            $version = $firstStep->version('A');

            $timezone = $campaign->timezone;
            $dateInTimeZone = Carbon::now($timezone);


            foreach ($prospects as $prospect) {
                $prospect['account_id'] = $account_id;
                $createdProspect = Prospect::create($prospect);
                $campaign->prospects()->attach($createdProspect->id, ['account_id' => $account_id]);

                CampaignMessage::query()->create([
                    'account_id' => $account_id,
                    'campaign_id' => $campaign->id,
                    'campaign_step_id' => $firstStep->id,
                    'campaign_step_version_id' => $version->id,
                    'prospect_id' => $createdProspect->id,
                    'available_at' => $dateInTimeZone,
                ]);
            }

            DB::commit();
            return $this->respondOk("Prospects were successfully created");
        } catch (Exception $error) {
            DB::rollBack();
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Prospect $campaign): JsonResponse
    {
        try {
            return $this->respondWithSuccess(new ProspectResource($campaign));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prospect $campaign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProspectUpdateRequest $request, Prospect $prospect): JsonResponse
    {
        try {
            $validated = $request->validated();
            $prospect->update($validated);
            return response()->json($prospect);
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prospect $prospect): JsonResponse
    {
        try {
            $prospect->delete();
            return $this->respondOk("Prospect [{$prospect->first_name} {$prospect->last_name}] deleted successfully");
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    public function csvUpload(Request $request): JsonResponse
    {
        try {
            $campaignUuid = $request->input('campaign_id');
            $campaign = Campaign::where('uuid', $campaignUuid)->firstOrFail();

            $user = $request->user();
            $account_id = $user->account_id;

            if (!$campaign) {
                $this->respondNotFound('Campaign not found');
            }

            $firstStep = $campaign->step(1);
            $version = $firstStep->version('A');
            $timezone = $campaign->timezone;
            $dateInTimeZone = Carbon::now($timezone);

            if ($request->hasFile('csv_file')) {
                $file = $request->file('csv_file');

                if ($file->getClientOriginalExtension() === 'csv') {
                    $filePath = $file->getRealPath();
                    $handle = fopen($filePath, 'r');

                    if ($handle !== false) {
                        $headers = fgetcsv($handle);

                        while (($data = fgetcsv($handle)) !== false) {
                            $prospectData = [];

                            foreach ($headers as $index => $header) {
                                $prospectData[$header] = $data[$index];
                            }

                            $prospectData['account_id'] = $account_id;
                            $prospect = Prospect::create($prospectData);
                            $campaign->prospects()->attach($prospect->id, ['account_id' => $account_id]);

                            CampaignMessage::query()->create([
                                'account_id' => $account_id,
                                'campaign_id' => $campaign->id,
                                'campaign_step_id' => $firstStep->id,
                                'campaign_step_version_id' => $version->id,
                                'prospect_id' => $prospect->id,
                                'available_at' => $dateInTimeZone,
                            ]);
                        }

                        fclose($handle);
                    }

                    return $this->respondOk('Prospects uploaded successfully');
                } else {
                    return $this->respondError('Invalid file format. Please upload a CSV file');
                }
            } else {
                return $this->respondError('No file uploaded');
            }
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }
}
