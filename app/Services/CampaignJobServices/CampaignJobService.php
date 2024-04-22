<?php

namespace App\Services\CampaignJobServices;

use App\Models\CampaignMessage;
use App\Models\RedisJob;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CampaignJobService
{
    public function deleteQueueElements($campaign): void
    {
        try {
            $jobIds = RedisJob::where("campaign_id", $campaign->id)
                ->pluck('redis_job_id');
            Log::alert('JobIds:' . json_encode($jobIds));

            $redisHorizon = Redis::connection('horizon');

            if(count($jobIds) > 0) {
                $removedIds = $redisHorizon->command('del', $jobIds->toArray());
                Log::alert('RemovedIds: ' . $removedIds);

                $removedPendingJobIds = $redisHorizon->command('ZREM', ['pending_jobs', $jobIds->toArray()]);
                Log::alert('RemovedPendingJobs: ' . $removedPendingJobIds);

                $removedRecentJobIds = $redisHorizon->command('ZREM', ['recent_jobs', $jobIds->toArray()]);
                Log::alert('RemovedRecentJobs: ' . $removedRecentJobIds);

                $removedQueuesCampaignDelayedJobIds = $this->deleteJobsByIds('default','queues:campaign:delayed', $jobIds->toArray());
                Log::alert('RemovedQueuesCampaignDelayedJobs: ' . $removedQueuesCampaignDelayedJobIds);
            }

            $campaign->update(['setup_campaign_job_id' => null]);

            CampaignMessage::where('status', 'scheduled')->where('campaign_id', $campaign->id)->update(['status' => 'pending']);

            RedisJob::whereIn('job_id', $jobIds)->delete();
        } catch (Exception $error) {
            Log::error('DeleteQueueElements: ' . $error->getMessage());
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
