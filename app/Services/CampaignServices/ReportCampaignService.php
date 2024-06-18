<?php

namespace App\Services\CampaignServices;

use DateInterval;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Log;
use App\Models\Campaign;
use Exception;

class ReportCampaignService {

    public Campaign $campaign;
    public array $reportInfo;

    public DateTime $from;
    public DateTime $to;

    /**
     * @throws Exception
     */
    public function __construct(Campaign $campaign, array $reportInfo)
    {
        $this->campaign = $campaign;
        $this->reportInfo = $reportInfo;
        $timezone = new DateTimeZone($this->campaign->timezone);

        switch ($this->reportInfo['period']['id']){
            case 1:
                $from = new DateTime('now', $timezone);
                $to = new DateTime('now', $timezone);

                $this->from = $from->setTime(0, 0);
                $this->to = $to->setTime(23, 59, 59);

                Log::channel('dev-campaign-report')->alert('From: ' . json_encode($this->from));
                Log::channel('dev-campaign-report')->alert('To: ' . json_encode($this->to));
                break;
            case 2:
                break;
        }
    }

    public function generate()
    {
        try {

            $campaignStatisticService = new StatisticCampaignService($this->campaign);
            $sent = $campaignStatisticService->sentTime($this->from, $this->to);

            Log::channel('dev-campaign-report')->alert('Sent: ' . $sent);

            $data = [
                ['campaign_id', 'campaign', 'campaign_status', 'mailbox', 'sent'],
                [$this->campaign->id, $this->campaign->name, $this->campaign->status, $this->campaign->mailbox->email, $sent],
            ];

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');

                foreach ($data as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return $callback;
        } catch (Exception $error) {
            Log::channel('dev-campaign-report')->error('ReportCampaignService generate(): ' . $error->getMessage());
            return 0;
        }
    }
}
