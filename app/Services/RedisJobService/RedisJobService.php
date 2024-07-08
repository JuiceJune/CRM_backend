<?php

namespace App\Services\RedisJobService;

use App\Models\CampaignMessage;
use App\Models\RedisJob;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RedisJobService
{
    private \Illuminate\Redis\Connections\Connection $redisHorizon;

    public function __construct()
    {
        $this->redisHorizon = Redis::connection('horizon');
    }

    public function createJob(array $data)
    {
        try {
            //
        } catch (Exception $e) {
            Log::error('Error creating Redis job: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getJob(string $id): ?array
    {
        try {
            $job = [];

            $job['id'] = $id;
            $job['jobName'] = $this->redisHorizon->hget($id, 'name');
            $job['jobCreatedAt'] = $this->redisHorizon->hget($id, 'created_at');
            $job['payload'] = $this->redisHorizon->hget($id, 'payload');
            $job['status'] = $this->redisHorizon->hget($id, 'status');
            $job['updatedAt'] = $this->redisHorizon->hget($id, 'updated_at');
            $job['connection'] = $this->redisHorizon->hget($id, 'connection');
            $job['queue'] = $this->redisHorizon->hget($id, 'queue');
            $job['completedAt'] = $this->redisHorizon->hget($id, 'completed_at');
            $job['reservedAt'] = $this->redisHorizon->hget($id, 'reserved_at');

            $job['payload'] = json_decode($job['payload'], true);
            $job['payload']['data']['command'] = unserialize($job['payload']['data']['command']);

            Log::channel('dev-campaign-process')->alert('Job: ' . json_encode($job));

            return $job;
        } catch (Exception $e) {
            Log::channel('dev-campaign-process')->error('Error fetching Redis job: ' . $e->getMessage());
            return null;
        }
    }

    public function updateJob(string $uuid, array $data): bool
    {
        try {
            // Оновлення роботи в базі даних
            $job = RedisJob::where('uuid', $uuid)->first();
            if ($job) {
                $job->update($data);

                // Оновлення роботи в Redis
                $this->redisHorizon->set("job:{$uuid}", json_encode($data));
                return true;
            }
            return false;
        } catch (Exception $e) {
            Log::error('Error updating Redis job: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteJob(string $uuid): bool
    {
        try {
            // Видалення роботи з бази даних
            $job = RedisJob::where('uuid', $uuid)->first();
            if ($job) {
                $job->delete();

                // Видалення роботи з Redis
                $this->redisHorizon->del("job:{$uuid}");
                return true;
            }
            return false;
        } catch (Exception $e) {
            Log::error('Error deleting Redis job: ' . $e->getMessage());
            return false;
        }
    }
}
