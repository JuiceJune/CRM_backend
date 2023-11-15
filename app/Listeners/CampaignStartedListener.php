<?php

namespace App\Listeners;

use App\Events\CampaignStarted;
use App\Events\QueueStarted;
use App\Jobs\MailJob;
use App\Jobs\StartQueue;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;

class CampaignStartedListener
{
    use InteractsWithQueue;

    public function handle(CampaignStarted $event)
    {
        $campaignId = $event->campaign->id;
        $queueName = "campaign_$campaignId";

        Log::channel('development')->alert('CampaignStartedListener');
        $prospects = $event->campaign->prospects;
        $delay = $event->campaign->period;
        $usedProspectsCount = 0;
        $date = Date::now($event->campaign->timezone);

        while (count($prospects) > $usedProspectsCount) {
            Log::channel('development')->alert('count($prospects): ' . count($prospects));
            Log::channel('development')->alert('$usedProspectsCount: ' . $usedProspectsCount);

            $startAdnEndTimeDateForSending = $this->getStartAndEndTimeDateForSending($event->campaign, $date);
            if($startAdnEndTimeDateForSending) {
                $usedProspectsCount += $this->ScheduleEmailsForToday($startAdnEndTimeDateForSending, $delay,
                    $prospects, $usedProspectsCount, $event->campaign);
            }
            $date->addDay()->startOfDay();
        }
        StartQueue::dispatch($queueName);
//        QueueStarted::dispatch($queueName);
    }

    private function ScheduleEmailsForToday($startAdnEndTimeDateForSending, $delay, $prospects, $usedProspectsCount, $campaign) {
        $startTime = $startAdnEndTimeDateForSending["startTime"];
        $startTime->setTimezone('Europe/Kiev');
        $endTime = $startAdnEndTimeDateForSending["endTime"];
        $endTime->setTimezone('Europe/Kiev');
        $emailsCount = floor($endTime->diffInSeconds($startTime) / $delay);

        Log::channel('development')->alert('$startTime: ' . $startTime);
        Log::channel('development')->alert('$endTime: ' . $endTime);
        Log::channel('development')->alert('$delay: ' . $delay);
        Log::channel('development')->alert('diff: ' . $endTime->diffInSeconds($startTime));
        Log::channel('development')->alert('$emailsCount: ' . $emailsCount);

        for ($i = $usedProspectsCount, $j = 0; $i <= $usedProspectsCount + $emailsCount; $i++, $j++) {
            if(count($prospects) > $i) {
                $sendingTime = $j === 0 ? $startTime : $startTime->addSeconds($delay);
                $this->scheduleMail($sendingTime, $prospects[$i], $campaign);
            } else {
                break;
            }
        }
        return $emailsCount;
    }

    private function getStartAndEndTimeDateForSending(Campaign $campaign, Carbon $date)
    {
        $sendingTimeData = $campaign->sending_time_json;

        $dayOfWeek = $this->getShortDayOfWeek($date->dayOfWeek);
        Log::channel('development')->alert('Day of week: ' . $dayOfWeek);

        if (array_key_exists($dayOfWeek, $sendingTimeData) && $sendingTimeData[$dayOfWeek][0] === true) {
            Log::channel('development')->alert('$isWeekdayTrue: true');

            $sendingTime = $sendingTimeData[$dayOfWeek];

            $sendingTimeStart = $sendingTime[1];
            Log::channel('development')->alert('start: ' . $sendingTimeStart);

            $sendingTimeEnd = $sendingTime[2];
            Log::channel('development')->alert('end: ' . $sendingTimeEnd);

            $currentTime = $date->format("H:i");
            Log::channel('development')->alert('NOW: ' . $currentTime);

            if ($currentTime < $sendingTimeStart) {
                Log::channel('development')->alert('Зараз до початку часу надсилання');
                $dateNowStart = Carbon::parse($sendingTimeStart, $campaign->timezone);
                $dateNowStart->setDate($date->year, $date->month, $date->day);

                $dateNowEnd = Carbon::parse($sendingTimeEnd, $campaign->timezone);
                $dateNowEnd->setDate($date->year, $date->month, $date->day);

                return [
                    "startTime" => $dateNowStart,
                    "endTime" => $dateNowEnd,
                ];
            } else if ($currentTime >= $sendingTimeStart && $currentTime <= $sendingTimeEnd) {
                Log::channel('development')->alert('Зараз час надсилання');
                return [
                    "startTime" => $date,
                    "endTime" => Carbon::parse($sendingTimeEnd, $campaign->timezone),
                ];
            } else {
                Log::channel('development')->alert('Зараз після закінчення часу надсилання');
                return null;
            }
        } else {
            Log::channel('development')->alert('$isWeekdayTrue: false');
            return null;
        }
    }


    private function getSendingTime(Campaign $campaign, int $index, int $delay)
    {
        $sendingTimeData = $campaign->sending_time_json;
        $date = Date::now($campaign->timezone)->addSeconds($delay * $index);
        $sendingDateTime = null;

        for ($i = 0; $i < 7; $i++) {
            $dayOfWeek = $this->getShortDayOfWeek(($date->dayOfWeek) % 7);
            Log::channel('development')->alert('Day of week: ' . $dayOfWeek . " - " . $i);

            if (array_key_exists($dayOfWeek, $sendingTimeData) && $sendingTimeData[$dayOfWeek][0] === true) {
                Log::channel('development')->alert('$isWeekdayTrue: true');

                $sendingTime = $sendingTimeData[$dayOfWeek];

                $sendingTimeStart = $sendingTime[1];
                Log::channel('development')->alert('start: ' . $sendingTimeStart);

                $sendingTimeEnd = $sendingTime[2];
                Log::channel('development')->alert('end: ' . $sendingTimeEnd);

                $currentTime = $date->format("H:i");
                Log::channel('development')->alert('NOW: ' . $currentTime);

                if ($currentTime < $sendingTimeStart) {
                    Log::channel('development')->alert('Зараз до початку часу надсилання');
                    $sendingDateTime = $date->setTimeFromTimeString($sendingTimeStart);
                    $sendingDateTime = $sendingDateTime->addSeconds($delay * $index);
                    break;
                } else if ($currentTime >= $sendingTimeStart && $currentTime <= $sendingTimeEnd) {
                    Log::channel('development')->alert('Зараз час надсилання');
                    $sendingDateTime = $date->addSeconds($delay * $index);
                    break;
                } else {
                    Log::channel('development')->alert('Зараз після закінчення часу надсилання');
                    $date = $date->addDay()->startOfDay();
                }
            } else {
                Log::channel('development')->alert('$isWeekdayTrue: false');
            }
        }
        return $sendingDateTime;
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

    function scheduleMail($sendingTime, $prospect, $campaign)
    {
        Log::channel('development')->alert('Sending Time: ' . $sendingTime . " | prospect: ". $prospect->id);
            MailJob::dispatch($prospect)
                ->onQueue('campaign_' . $campaign->id)
                ->delay($sendingTime);
    }
}

