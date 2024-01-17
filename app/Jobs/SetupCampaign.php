<?php

namespace App\Jobs;

use App\Http\Resources\EmailJobResource;
use App\Models\Campaign;
use App\Models\CampaignStep;
use App\Models\CampaignStepProspect;
use App\Models\EmailJob;
use App\Models\Prospect;
use Carbon\Carbon;
use DateTime;
use Illuminate\Bus\Dispatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetupCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle()
    {
        $currentDateTime = Carbon::now($this->campaign->timezone);  // Get current Date by TimeZone

        Log::channel('development')->alert('CAMPAIGN: : ' . $this->campaign->name . "[id: " . $this->campaign->id . "]");
        Log::channel('development')->alert('Current DateTime (' . $this->campaign->timezone . '): ' . $currentDateTime);

        $stepsProspectsQuantity = $this->getStepsAndProspectsQuantityToday($currentDateTime); // Get array of available steps today
        Log::channel('development')->alert('Campaign step and prospects count: ' . json_encode($stepsProspectsQuantity));

        if(count($stepsProspectsQuantity) < 1) {
            //TODO There are not available prospects now
            return;
        }
        $stepsAmount = count($stepsProspectsQuantity); // Amount of available steps
        $stepsPriorityConfig = $this->campaign->priority_config; // Get Priority Config
        $stepsPercentArray = $stepsPriorityConfig[$stepsAmount - 1]; // Get Percents
        Log::channel('development')->alert('Steps priority config: ' . json_encode($stepsPriorityConfig));
        Log::channel('development')->alert('Current steps percent array: ' . json_encode($stepsPercentArray));

        $campaignSendLimit = $this->campaign->send_limit; // Get Campaign send limit
        Log::channel('development')->alert('Campaign Send Limit: ' . $campaignSendLimit);

        $freeSlots = 0; // Free slots of sending -> it can transfer to other steps

        foreach (array_reverse($stepsProspectsQuantity) as $key => $stepProspectsQuantity) { // Loop of steps Form END to START 10..1
            $stepNumber = $stepProspectsQuantity["step"]; // Get number of step 1..10
            $step = $this->campaign->step($stepNumber); // Get step object
            $dayNumber = $currentDateTime->dayOfWeek; // Get Number of day 0..6
            $dayShortName = $this->getShortDayOfWeek($dayNumber); // Get Short version of day name (Monday - Mon)
            $stepSendingInfoForToday = $step->sending_time_json[$dayShortName]; // Get info like [true, "08:00", "15:00"]

            Log::channel('development')->alert('STEP: ' . $stepNumber);

            if(!$stepSendingInfoForToday[0]) { // If today is not sanding day for this step
                Log::channel('development')->alert('Today is not available day for sending this step');
                //TODO Today is not available day for sending this step
                continue;
            }

            $startEndDate = $this->getStartEndTimeForSendingToday($currentDateTime, $stepSendingInfoForToday); // Get Start and End dateTime of sending today

            if(!$startEndDate) { // if sending step time end for today
                Log::channel('development')->alert('Sending time for this step is over for today');
                //TODO Sending time for this step is over for today
                continue;
            }

            $stepSendLimit = $this->getConfigurationStepSendLimit($step, $startEndDate['start'], $startEndDate['end']); // Get send limit by this info [true, "08:00", "15:00"] and period

            $prospectsQuantity = $stepProspectsQuantity["count"]; // get amount of prospects available for this step
            $percent = $stepsPercentArray[count($stepsPercentArray) - 1 - $key]; // Get percent max limit for this step

            Log::channel('development')->alert('Percent: ' . $percent);

            $campaignPercentStepLimit = $campaignSendLimit * $percent / 100; // Get max limit for this step in numbers

            Log::channel('development')->alert('Campaign percent step limit: ' . $campaignPercentStepLimit);
            Log::channel('development')->alert('Step send limit: ' . $stepSendLimit);
            Log::channel('development')->alert('Prospects quantity: ' . $prospectsQuantity);

            $currentLimit = $campaignPercentStepLimit;

            if($currentLimit > $stepSendLimit) { // If Campaign Send limit is bigger than stepSendLimit -> set currentLimit = stepSendLimit
                $currentLimit = $stepSendLimit;
            }

            if($currentLimit > $prospectsQuantity) { // If currentLimit bigger than prospectsQuantity -> set currentLimit = prospectsQuantity
                $currentLimit = $prospectsQuantity;
            }

            if($currentLimit < $campaignPercentStepLimit) {
                $freeSlots += $campaignPercentStepLimit - $currentLimit;
                Log::channel('development')->alert('Add to free slots: ' . ($campaignPercentStepLimit - $currentLimit));
                Log::channel('development')->alert('Free slots: ' . $freeSlots);
            } else if ($currentLimit < $stepSendLimit && $currentLimit < $prospectsQuantity && $freeSlots > 0) {
                $freeStepSendLimit = $stepSendLimit - $currentLimit;
                $freeProspectsAmount = $prospectsQuantity - $currentLimit;
                $minValue = min($freeProspectsAmount, $freeStepSendLimit, $freeSlots);
                $freeSlots -= $minValue;
                $currentLimit += $minValue;
                Log::channel('development')->alert('Get from free slots: ' . $minValue);
                Log::channel('development')->alert('Free slots: ' . $freeSlots);
            }

            $prospects = $this->campaign->prospectsByStepAndStatus($stepNumber, 'active', $currentLimit); // Get prospects
            Log::channel('development')->alert('Prospects: ' . count($prospects));
            $version = $step->version('A'); // get Version

            $currentDateSending = $startEndDate['start']; // start point of dateTime
            $period = $step->period; // get period between messages

            foreach ($prospects as $prospectKey => $prospect) {
                $prospect = new Prospect($prospect);
                $this->scheduleMail($currentDateSending, $prospect, $version);
                $currentDateSending->addSeconds($period);
            }
            Log::channel('development')->alert(PHP_EOL);
        }
        Log::channel('development')->alert('===================================================');
        Log::channel('development')->alert(PHP_EOL);

        $this->scheduleCampaignSetup();
    }

    private function getStepsAndProspectsQuantityToday(Carbon $currentDateTime) {
        $campaignStepProspects = CampaignStepProspect::join('campaign_steps', 'campaign_step_prospects.campaign_step_id', '=', 'campaign_steps.id')
            ->where('campaign_step_prospects.status', 'pending') // Status should be pending
            ->where('campaign_step_prospects.campaign_id', $this->campaign->id) // By current campaign
            ->where('campaign_step_prospects.available_at', '<=', $currentDateTime) // available_at should be less than today
            ->select('campaign_steps.step', DB::raw('count(*) as count')) // count prospects
            ->groupBy('campaign_steps.step') // Group by step name (1..10)
            ->get()
            ->toArray();
        return $campaignStepProspects;
    }

    private function getConfigurationStepSendLimit($step, $timeStart, $timeEnd) {
        Log::channel('development')->alert('$timeStart: ' . $timeStart);
        Log::channel('development')->alert('$timeEnd: ' . $timeEnd);
        $periodInSeconds = $step->period; // Get period - it is time between messages (in seconds)

        $secondsDifference = $timeStart->diffInSeconds($timeEnd); // get difference between start and end

        $todayStepSendLimit = $secondsDifference / $periodInSeconds; // Get amount of messages for today by step configuration

        return floor($todayStepSendLimit);
    }

    private function getStartEndTimeForSendingToday(Carbon $date, $stepSendingInfoForToday) {
        $timeStart = Carbon::parse($stepSendingInfoForToday[1])->format("H:i"); // Get start time
        $timeEnd = Carbon::parse($stepSendingInfoForToday[2])->format("H:i"); // Get end time
        return $this->getStartAndEndTimeDateForSending($date, $timeStart, $timeEnd);
    }

    private function getStartAndEndTimeDateForSending(Carbon $date, $timeStart, $timeEnd)
    {
        $currentTime = $date->setTimezone($this->campaign->timezone)->format("H:i");
        Log::channel('development')->alert('Now: ' . $currentTime);
        Log::channel('development')->alert('Time start: ' . $timeStart);
        Log::channel('development')->alert('Time end: ' . $timeEnd);

        if ($currentTime < $timeStart) {
            Log::channel('development')->alert('Зараз до початку часу надсилання');
            $dateNowStart = Carbon::parse($timeStart);
            $dateNowStart->setDate($date->year, $date->month, $date->day);

            $dateNowEnd = Carbon::parse($timeEnd);
            $dateNowEnd->setDate($date->year, $date->month, $date->day);

            return [
                "start" => $dateNowStart,
                "end" => $dateNowEnd,
            ];
        } else if ($currentTime >= $timeStart && $currentTime <= $timeEnd) {
            Log::channel('development')->alert('Зараз час надсилання');
            $date = $date->setTimezone($this->campaign->timezone);
            return [
                "start" => $date,
                "end" => Carbon::parse($timeEnd),
            ];
        } else {
            Log::channel('development')->alert('Зараз після закінчення часу надсилання');
            return null;
        }
    }

    function getShortDayOfWeek($dayOfWeek)
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

    function scheduleMail($sendingTime, $prospect, $version)
    {
        Log::channel('development')->alert('Sending Time: ' . $sendingTime . " | prospect: " . $prospect->id);

        $jobId = app(Dispatcher::class)
            ->dispatch(
                (new MailJob($prospect, $version))
                    ->onQueue('campaign')
                    ->delay($sendingTime)
            );

        CampaignStepProspect::where('campaign_id', $this->campaign->id)->where('prospect_id', $prospect->id)->where('campaign_step_id', $version->step->id)->update(['status' => "scheduled"]);
        EmailJob::create(["job_id" => $jobId, "campaign_step_version_id" => $version->id, 'campaign_step_id' => $version->step->id, 'campaign_id' => $version->step->campaign->id, "prospect_id" => $prospect['id']]);
    }

    function scheduleCampaignSetup()
    {
        $setupTime = Carbon::now($this->campaign->timezone)->addDay()->setTime(0, 1);;
        Log::channel('development')->alert('$setupTime: ' . $setupTime);

        SetupCampaign::dispatch($this->campaign)->delay($setupTime);
    }

    /////////////////////////////////OLD METHODS///////////////////////////////////////////////
    private function scheduleStepByProspect($step)
    {
        $prospects = $this->campaign->prospectsByStepAndStatus($step->step, 'active');
        $version = $step->version('A');

        Log::channel('development')->alert('Step ' . $step->step . ' | prospects: ' . json_encode($prospects));

        $delay = $step->period;
        $sendingTimeData = $step->sending_time_json;
        $startAfter = $step->start_after;
        $usedProspectsCount = 0;

        foreach ($prospects as $prospect) {
            $sentTimePreviousStep = CampaignStepProspect::where('prospect_id', $prospect['id'])
                ->where('status', 'sent')
                ->whereNotNull('sent_time')
                ->whereHas('campaignStepVersion.step', function ($query) use ($step) {
                    $query->where('step', $step->step - 1);
                })
                ->value('sent_time');

            $dateTime = Carbon::parse($sentTimePreviousStep);
            if ($startAfter["time_type"] == 'days') {
                $dateTime->addDays($startAfter["time"]);
            } else {
                $dateTime->addHours($startAfter["time"]);
            }
            Log::channel('development')->alert('Prospect: ' . $prospect['id'] . ' | dateTime: ' . $dateTime);
        }

        $date = now(); //TODO just corp

        while (count($prospects) > $usedProspectsCount) {
            $startAndEndTimeDateForSending = $this->getStartAndEndTimeDateForSending($this->campaign, $date, $sendingTimeData);
            if ($startAndEndTimeDateForSending) {
                $usedProspectsCount += $this->ScheduleEmailsForToday($startAndEndTimeDateForSending, $delay,
                    $prospects, $usedProspectsCount, $version);
            }
            $date->addDay()->startOfDay();
        }
    }

    private function scheduleDay($step)
    {
        $delay = $step->period;
        $sendingTimeData = $step->sending_time_json;
        $usedProspectsCount = 0;
        $date = Date::now($this->campaign->timezone);

        $startAndEndTimeDateForSending = $this->getStartAndEndTimeDateForSending($this->campaign, $date, $sendingTimeData);
//        if ($startAndEndTimeDateForSending) {
//            $usedProspectsCount += $this->ScheduleEmailsForToday($startAndEndTimeDateForSending, $delay,
//                $prospects, $usedProspectsCount, $version);
//        }
    }

    private function scheduleStep($step)
    {
        $prospects = $this->campaign->prospectsByStepAndStatus($step->step, 'active');
        $version = $step->version('A');

        Log::channel('development')->alert('Step ' . $step->step . ' | prospects: ' . json_encode($prospects));

        $delay = $step->period;
        $sendingTimeData = $step->sending_time_json;
        $usedProspectsCount = 0;
        $date = Date::now($this->campaign->timezone);

        while (count($prospects) > $usedProspectsCount) {
            $startAndEndTimeDateForSending = $this->getStartAndEndTimeDateForSending($this->campaign, $date, $sendingTimeData);
            if ($startAndEndTimeDateForSending) {
                $usedProspectsCount += $this->ScheduleEmailsForToday($startAndEndTimeDateForSending, $delay,
                    $prospects, $usedProspectsCount, $version);
            }
            $date->addDay()->startOfDay();
        }
    }

    private function ScheduleEmailsForToday($startAdnEndTimeDateForSending, $delay, $prospects, $usedProspectsCount, $version)
    {
        $startTime = $startAdnEndTimeDateForSending["startTime"];
        $startTime->setTimezone($this->campaign->timezone);
        $endTime = $startAdnEndTimeDateForSending["endTime"];
        $endTime->setTimezone($this->campaign->timezone);
        $emailsCount = floor($endTime->diffInSeconds($startTime) / $delay);

        for ($i = $usedProspectsCount, $j = 0; $i <= $usedProspectsCount + $emailsCount; $i++, $j++) {
            if (count($prospects) > $i) {
                $sendingTime = $j === 0 ? $startTime : $startTime->addSeconds($delay);
                $this->scheduleMail($sendingTime, Prospect::find($prospects[$i]["id"]), $version);
            } else {
                break;
            }
        }
        return $emailsCount;
    }
}


