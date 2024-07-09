<?php

namespace App\Http\Controllers\Api\ScheduledEmail;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CampaignStoreRequest;
use App\Http\Requests\Campaign\CampaignUpdateRequest;
use App\Http\Resources\Campaign\CampaignShowResource;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\RedisJob;
use App\Services\RedisJobService\RedisJobService;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduledEmail extends Controller
{
    use ApiResponseHelpers;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Campaign $campaign): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();
            $accountId = $user->account_id;

            $campaignRedisJobs = RedisJob::query()
                ->where('account_id', $accountId)
                ->where('campaign_id', $campaign['id'])
                ->where('type', 'campaign-email-send')
                ->get();

            if (!$campaignRedisJobs || count($campaignRedisJobs) == 0) {
                return $this->respondWithSuccess([
                    'jobs' => null,
                ]);
            }

            $redisJobService = new RedisJobService();
            $scheduledEmails = [];

            foreach ($campaignRedisJobs as $campaignRedisJob) {
                $job = $redisJobService->getJob($campaignRedisJob['redis_job_id']);

                $campaignMessageId = $job["payload"]["data"]["command"]->campaignMessage["id"];
                $campaignMessage = CampaignMessage::query()->find($campaignMessageId);

                $scheduledEmails[] = [
                    "id" => $job['id'],
                    "jobCreated" => $job["jobCreatedAt"],
                    "status" => $job["status"],
                    "delay" => $job["payload"]["data"]["command"]->delay,
                    "email" => $campaignMessage->prospect["email"],
                    "step" => $campaignMessage->campaignStep['step'],
                    "version" => $campaignMessage->campaignStepVersion['version']
                ];
            }

            return $this->respondWithSuccess([
                'jobs' => $scheduledEmails,
            ]);
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            //
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CampaignStoreRequest $request)
    {
        try {
            //
        } catch (\Exception $error) {
            DB::rollBack();
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->respondWithSuccess(new CampaignShowResource($campaign));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        try {
            //
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CampaignUpdateRequest $request, Campaign $campaign)
    {
        try {
            //
        } catch (\Exception $error) {
            DB::rollBack();
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign): \Illuminate\Http\JsonResponse
    {
        try {
            $campaign->delete();
            return $this->respondOk($campaign->name);
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }
}
