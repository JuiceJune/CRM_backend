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
use App\Services\ProspectServices\ProspectService;
use App\Services\ProspectServices\StoreProspectService;
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
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            $sortField = $request->input('sortField');
            $sortOrder = $request->input('sortOrder');
            $filters = $request->input('filters');
            $globalFilter = $request->input('globalFilter');

            $query = $campaign_id
                ? Campaign::query()->where('uuid', $campaign_id)->firstOrFail()->prospects()
                : Prospect::query();

            // Apply filters
            if ($filters) {
                foreach ($filters as $key => $filter) {
                    if (!empty($filter['value']) && !empty($filter['matchMode'])) {
                        switch ($filter['matchMode']) {
                            case 'contains':
                                $query->where($key, 'like', '%' . $filter['value'] . '%');
                                break;
                            case 'notContains':
                                $query->whereNot($key, 'like', '%' . $filter['value'] . '%');
                                break;
                            case 'startsWith':
                                $query->where($key, 'like', $filter['value'] . '%');
                                break;
                            case 'endsWith':
                                $query->where($key, 'like', '%' . $filter['value']);
                                break;
                            case 'equals':
                                $query->where($key, "=", $filter['value']);
                                break;
                            case 'notEquals':
                                $query->where($key, "!=", $filter['value']);
                                break;
                            default:
                                $query->where($key, $filter['value']);
                                break;
                        }
                    }
                }
            }

            if ($globalFilter) {
                $query->where(function($query) use ($globalFilter) {
                    $query->where('prospects.email', 'like', '%' . $globalFilter . '%')
                        ->orWhere('prospects.status', 'like', '%' . $globalFilter . '%')
                        ->orWhere('prospects.first_name', 'like', '%' . $globalFilter . '%')
                        ->orWhere('prospects.last_name', 'like', '%' . $globalFilter . '%');
                });
            }

            // Apply sorting
            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            $prospects = $query->paginate($limit, ['*'], 'page', $page);

            if($campaign_id) {
                return response()->json(ProspectCampaignResource::collection($prospects)->response()->getData(true));
            } else {
                return response()->json(ProspectCampaignResource::collection($prospects)->response()->getData(true));
            }
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
            $accountId = $user->account_id;
            $prospects = $validated['prospects'];
            $campaignUuid = $validated['campaign_id'];

            $campaign = Campaign::where('uuid', $campaignUuid)->firstOrFail();

            $storeProspectService = new StoreProspectService();
            $results = $storeProspectService->storeProspects($prospects, $campaign, $accountId);

            DB::commit();
            return $this->respondWithSuccess([
                'successProspects' => ProspectResource::collection($results['successProspects']),
                'errorProspects' => [],
                'duplicateProspects' => $results['duplicateProspects'],
            ]);
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
            $campaign = $prospect->campaigns->first();
            $project = $campaign->project;

            if (isset($validated['email'])) {
                $duplicateProspect = Prospect::where('email', $validated['email'])
                    ->whereHas('projects', function ($query) use ($project) {
                        $query->where('project_id', $project->id);
                    })
                    ->where('id', '!=', $prospect->id)
                    ->first();

                if ($duplicateProspect) {
                    return $this->respondError("A prospect with this email already exists in the same project.");
                }
            }

            if(isset($validated["status"]) && $campaign) {
                $prospectService = new ProspectService($prospect, $campaign);
                if(!$prospectService->changeStatus($validated["status"]))
                    return $this->respondError("Prospect wasn't updated");
            }
            $prospect->update($validated);
            return $this->respondOk("Prospect was successfully updated");
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
            if ($request->hasFile('csv_file')) {
                $file = $request->file('csv_file');

                if ($file->getClientOriginalExtension() === 'csv') {
                    $filePath = $file->getRealPath();
                    $handle = fopen($filePath, 'r');

                    if ($handle !== false) {
                        $headers = (new \App\Models\Prospect)->getFillable();
                        $fieldsToExclude = ["account_id", "date_added", "tags", 'status'];
                        $headers = array_diff($headers, $fieldsToExclude);
                        $headers = array_values($headers);
                        array_unshift($headers, 'none');

                        $prospects = [];

                        for ($i = 0; ($data = fgetcsv($handle)) !== false; $i++) {
                            $prospects[] = $data;
                        }

                        fclose($handle);

                        return response()->json([
                            'success' => true,
                            'headers' => $headers,
                            'prospects' => $prospects,
                        ]);
                    } else {
                        return $this->respondError('Unable to open CSV file');
                    }
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

    public function csvProspectsSave(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $campaignUuid = $request->input('campaign_id');
            $prospects = $request->input('prospects');
            $headers = $request->input('headers');

            $campaign = Campaign::where('uuid', $campaignUuid)->firstOrFail();

            $user = $request->user();
            $accountId = $user->account_id;

            $storeProspectService = new StoreProspectService();
            $results = $storeProspectService->storeProspectsWithHeaders($prospects, $headers, $campaign, $accountId);

            DB::commit();
            return $this->respondWithSuccess([
                'successProspects' => ProspectResource::collection($results['successProspects']),
                'errorProspects' => $results['errorProspects'],
                'duplicateProspects' => $results['duplicateProspects'],
            ]);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->respondError($error->getMessage());
        }
    }

    public function uniqueCheck(Request $request): JsonResponse
    {
        try {
            $campaignUuid = $request->input('campaign_id');
            $email = $request->input('email');

            $campaign = Campaign::where('uuid', $campaignUuid)->firstOrFail();
            $project = $campaign->project;

            $duplicateProspect = Prospect::where('email', $email)
                ->whereHas('projects', function ($query) use ($project) {
                    $query->where('project_id', $project->id);
                })
                ->first();

            if ($duplicateProspect) {
                return $this->respondError("A prospect with this email already exists in the same project.");
            } else {
                return $this->respondOk("Unique prospect");
            }
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }
}
