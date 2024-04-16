<?php

namespace App\Services\QueuesHandleServices;

use App\Models\EmailJob;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class QueuesStopElementService
{
    public function deleteQueueElements($campaign, $prospectId)
    {
        try {
            $jobIds = EmailJob::where("campaign_id", $campaign->id)
                ->where('prospect_id', $prospectId)
                ->pluck('job_id');

            $redisHorizon = Redis::connection('horizon');

            if(count($jobIds) > 0) {
                $removedIds = $redisHorizon->command('del', $jobIds->toArray());

                $removedPendingJobIds = $redisHorizon->command('ZREM', ['pending_jobs', $jobIds->toArray()]);

                $removedRecentJobIds = $redisHorizon->command('ZREM', ['recent_jobs', $jobIds->toArray()]);

                $removedQueuesCampaignDelayedJobIds = $this->deleteJobsByIds('default','queues:campaign:delayed', $jobIds->toArray());
            }

            EmailJob::whereIn('job_id', $jobIds)->delete();
        } catch (Exception $error) {
            Log::channel('development')->error("Error: " . $error->getMessage());
        }
    }

    private function deleteJobsByIds($connection, $element, $ids) {
        try {
            $redisDefault = Redis::connection($connection);
            $removedQueuesDefaultDelayedJobIds = $redisDefault->command('ZRANGE', [$element, '0', '-1']);
            $deletedCount = 0;

            if (!is_array($ids)) {
                $ids = [$ids];
            }

            foreach ($removedQueuesDefaultDelayedJobIds as $item) {
                $item = json_decode($item, true);
                if (in_array($item['uuid'], $ids)) {
                    $item = json_encode($item);
                    $deletedCount += $redisDefault->zrem($element, $item);
                }
            }

            return $deletedCount;
        } catch (Exception $error) {
            throw new \Error($error);
        }
    }
}