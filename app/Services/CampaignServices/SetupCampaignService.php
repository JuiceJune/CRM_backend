<?php

namespace App\Services\CampaignServices;

use App\Jobs\MailJob;
use App\Jobs\SetupCampaignJob;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\RedisJob;
use Carbon\Carbon;
use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetupCampaignService
{
    public Campaign $campaign;
    private Carbon $dateTime;
    private array $priorityConfig;
    private int $sendLimit;
    private int $freeSlots = 0;
    private array $priorityPercentArray;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
        $this->priorityConfig = $campaign->priority_config;
        $this->sendLimit = $campaign->send_limit;
        $this->dateTime = Carbon::now($campaign->timezone);
    }

    /**
     * @throws \Exception
     */
    public function setup(): void
    {
        try {
            Log::channel('dev-campaign-process')->alert('Setup[' . $this->campaign->name . ']===============START');

            if($this->campaign->status === 'stopped') {
                Log::channel('dev-campaign-process')->alert('Setup[' . $this->campaign->name . '] - STOPPED');
                return;
            }

            $stepsProspectsCount = $this->getAvailableProspectCountForEachStep();
            Log::channel('dev-campaign-process')->alert(json_encode("Step prospects count"));
            Log::channel('dev-campaign-process')->alert(json_encode($stepsProspectsCount));

            $stepsAmount = count($stepsProspectsCount); // Amount of available steps

            if ($stepsAmount < 1) {
                return;
            }

            $this->priorityPercentArray = $this->priorityConfig[$stepsAmount - 1]; // Get Percents
            Log::channel('dev-campaign-process')->alert('Priority config array');
            Log::channel('dev-campaign-process')->alert(json_encode($this->priorityPercentArray));

            foreach (array_reverse($stepsProspectsCount) as $key => $stepProspectsCount) { // Loop of steps Form END to START 10..1
                $this->setupStep($key, $stepProspectsCount);
            }

        } catch (\Exception $error) {
            Log::error(json_encode($error));
        } finally {
            $this->scheduleCampaignSetup();
            Log::channel('dev-campaign-process')->alert('Setup[' . $this->campaign->name . "]===============END\n");
        }
    }

    private function setupStep($key, $stepProspectsCount): void
    {
        try {
            $stepNumber = $stepProspectsCount["step"]; // Get number of step 1..10
            $step = $this->campaign->step($stepNumber); // Get step object

            Log::channel('dev-campaign-process')->alert("Step: " . $stepNumber);
            Log::channel('dev-campaign-process')->alert(json_encode($step));

            $dayNumber = $this->dateTime->dayOfWeek; // Get Number of day 0..6
            $dayShortName = $this->getShortDayOfWeek($dayNumber); // Get Short version of day name (Monday - Mon)

            $stepSendingInfoForToday = $step->sending_time_json[$dayShortName]; // Get info like [true, "08:00", "15:00"]

            if (!$stepSendingInfoForToday[0]) { // If today is not sanding day for this step
                return;
            }

            $startEndDate = $this->getStartEndTimeForSendingToday($this->dateTime, $stepSendingInfoForToday); // Get Start and End dateTime of sending today

            if (!$startEndDate) { // if sending step time end for today
                return;
            }

            $stepSendLimit = $this->getConfigurationStepSendLimit($step, $startEndDate['start'], $startEndDate['end']); // Get send limit by this info [true, "08:00", "15:00"] and period
            Log::channel('dev-campaign-process')->alert('Step Send limit: ' . json_encode($stepSendLimit));

            $prospectsQuantity = $stepProspectsCount["count"]; // get amount of prospects available for this step
            Log::channel('dev-campaign-process')->alert('Amount of prospects available for this step: ' . $prospectsQuantity);

            $percent = $this->priorityPercentArray[count($this->priorityPercentArray) - $key - 1]; // Get percent max limit for this step
            Log::channel('dev-campaign-process')->alert('Get percent max limit for this step: ' . $percent);


            $campaignPercentStepLimit = $this->sendLimit * $percent / 100; // Get max limit for this step in numbers
            Log::channel('dev-campaign-process')->alert('Get max limit for this step in numbers: ' . $campaignPercentStepLimit);

            $currentLimit = $campaignPercentStepLimit;
            Log::channel('dev-campaign-process')->alert('Current limit: ' . $currentLimit);


            if ($currentLimit > $stepSendLimit) { // If Campaign Send limit is bigger than stepSendLimit -> set currentLimit = stepSendLimit
                $currentLimit = $stepSendLimit;
            }

            if ($currentLimit > $prospectsQuantity) { // If currentLimit bigger than prospectsQuantity -> set currentLimit = prospectsQuantity
                $currentLimit = $prospectsQuantity;
            }

            if ($currentLimit < $campaignPercentStepLimit) {
                $this->freeSlots += $campaignPercentStepLimit - $currentLimit;
            } else if ($currentLimit < $stepSendLimit && $currentLimit < $prospectsQuantity && $this->freeSlots > 0) {
                $freeStepSendLimit = $stepSendLimit - $currentLimit;
                $freeProspectsAmount = $prospectsQuantity - $currentLimit;
                $minValue = min($freeProspectsAmount, $freeStepSendLimit, $this->freeSlots);
                $this->freeSlots -= $minValue;
                $currentLimit += $minValue;
            }

            Log::channel('dev-campaign-process')->alert('currentLimit: ' . $currentLimit);

            $campaignMessages = $this->getPendingCampaignMessagesByStep($step, $currentLimit); // Get prospects
            Log::channel('dev-campaign-process')->alert('ProspectMessages: ' . count($campaignMessages));
            Log::channel('dev-campaign-process')->alert('ProspectMessages: ' . json_encode($campaignMessages));

            $version = $step->version('A'); // get Version

            $this->dateTime = $startEndDate['start']; // start point of dateTime
            Log::channel('dev-campaign-process')->alert('Start point of dateTime: ' . $this->dateTime);

            $period = $step->period; // get period between messages
            Log::channel('dev-campaign-process')->alert('Period: ' . $period);

            foreach ($campaignMessages as $campaignMessage) {
                Log::channel('dev-campaign-process')->alert('Message: ');
                Log::channel('dev-campaign-process')->alert(json_encode($campaignMessage));

                $this->scheduleMail($campaignMessage);
                $this->dateTime->addSeconds($period);
            }
        } catch (\Exception $error) {
            Log::channel('dev-campaign-process')->error(json_encode($error));
        }
    }

    private function getAvailableProspectCountForEachStep(): array
    {
        return CampaignMessage::query()->join(
            'campaign_steps', 'campaign_messages.campaign_step_id', '=', 'campaign_steps.id')
            ->where('campaign_messages.account_id', $this->campaign->account_id) // By current account
            ->where('campaign_messages.campaign_id', $this->campaign->id) // By current campaign
            ->where('campaign_messages.status', 'pending') // Status should be pending
            ->where('campaign_messages.available_at', '<=', $this->dateTime) // available_at should be less than today
            ->select('campaign_steps.step', DB::raw('count(*) as count')) // count prospects
            ->groupBy('campaign_steps.step') // Group by step name (1..10)
            ->get()
            ->toArray();
    }

    private function getPendingCampaignMessagesByStep($step, $limit): \Illuminate\Database\Eloquent\Collection|array
    {
        return $step->messages()
            ->where('status', 'pending')
            ->where('available_at', '<=', $this->dateTime)
            ->take($limit)
            ->get();
    }

    private function getShortDayOfWeek($dayOfWeek): string
    {
        $shortDaysOfWeek = [
            0 => "Sun",
            1 => "Mon",
            2 => "Tues",
            3 => "Wed",
            4 => "Thurs",
            5 => "Fri",
            6 => "Sat",
        ];
        return $shortDaysOfWeek[$dayOfWeek];
    }

    private function getStartEndTimeForSendingToday(Carbon $date, $stepSendingInfoForToday): ?array
    {
        try {
            $timeStart = Carbon::parse($stepSendingInfoForToday[1])->format("H:i"); // Get start time
            $timeEnd = Carbon::parse($stepSendingInfoForToday[2])->format("H:i"); // Get end time
            return $this->getStartAndEndTimeDateForSending($date, $timeStart, $timeEnd);
        } catch (\Exception $error) {
            Log::channel('dev-campaign-process')->error(json_encode($error));
            return null;
        }
    }

    private function getStartAndEndTimeDateForSending(Carbon $date, $timeStart, $timeEnd): ?array
    {
        try {
            $currentTime = $date->format("H:i");

            if ($currentTime < $timeStart) {
                return [
                    "start" => Carbon::parse($timeStart),
                    "end" => Carbon::parse($timeEnd),
                ];
            } else if ($currentTime <= $timeEnd) {
                ;
                return [
                    "start" => Carbon::parse($currentTime)->addMinute(),
                    "end" => Carbon::parse($timeEnd),
                ];
            } else {
                return null;
            }
        } catch (\Exception $error) {
            Log::channel('dev-campaign-process')->error(json_encode($error));
            return null;
        }
    }

    private function getConfigurationStepSendLimit($step, Carbon $timeStart, Carbon $timeEnd): ?float
    {
        try {
            $periodInSeconds = $step->period; // Get period - it is time between messages (in seconds)
            $secondsDifference = $timeStart->diffInSeconds($timeEnd); // get difference between start and end
            $todayStepSendLimit = $secondsDifference / $periodInSeconds; // Get amount of messages for today by step configuration

            return floor($todayStepSendLimit);
        } catch (\Exception $error) {
            Log::channel('dev-campaign-process')->error(json_encode($error));
            return null;
        }
    }

    private function scheduleMail($campaignMessage): void
    {
        try {
            $jobId = app(Dispatcher::class)->dispatch(
                (new MailJob($campaignMessage))
                    ->onQueue('campaign')
                    ->delay($this->dateTime)
            );

            $redisJob = RedisJob::create([
                "redis_job_id" => $jobId,
                "account_id" => $campaignMessage->account->id,
                "type" => 'campaign-email-send',
                "campaign_step_version_id" => $campaignMessage->campaignStepVersion->id,
                'campaign_step_id' => $campaignMessage->campaignStep->id,
                'campaign_id' => $campaignMessage->campaign->id,
                "prospect_id" => $campaignMessage->prospect->id,
                "status" => 'active',
                "date_time" => $this->dateTime
            ]);

            $campaignMessage->update([
                'redis_job_id' => $redisJob['id'],
                'status' => 'scheduled'
            ]);

            Log::channel('dev-campaign-process')->alert('Schedule email | Time: ' . $this->dateTime . " | JobId: " . $jobId . " | Message: " . json_encode($campaignMessage));
        } catch (\Exception $error) {
            Log::channel('dev-campaign-process')->error("ScheduleMail: " . $error->getMessage());
        }
    }

    private function scheduleCampaignSetup(): void
    {
        try {
            $setupTime = Carbon::now($this->campaign->timezone)->addDay()->setTime(0, 1, 1, 1);
            $jobId = app(Dispatcher::class)->dispatch((new SetupCampaignJob($this->campaign))->delay($setupTime));

            RedisJob::create([
                "redis_job_id" => $jobId,
                "account_id" => $this->campaign->account_id,
                "type" => 'campaign-setup',
                'campaign_id' => $this->campaign->id,
                "status" => 'active',
                "date_time" => $setupTime
            ]);

            Log::channel('dev-campaign-process')->alert('Schedule next campaign setup (tomorrow): ' . $setupTime . " | Job: " . $jobId);
        } catch (\Exception $error) {
            Log::channel('dev-campaign-process')->error('scheduleCampaignSetup: ' . $error->getMessage());
        }
    }
}
