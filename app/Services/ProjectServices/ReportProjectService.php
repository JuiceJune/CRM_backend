<?php

namespace App\Services\ProjectServices;

use App\Models\Project;
use App\Services\CampaignServices\StatisticCampaignService;
use DateTime;
use Illuminate\Support\Facades\Log;
use Exception;

class ReportProjectService {

    public Project $project;
    public array $reportInfo;

    public DateTime $from;
    public DateTime $to;

    /**
     * @throws Exception
     */
    public function __construct(Project $project, array $reportInfo)
    {
        $this->project = $project;
        $this->reportInfo = $reportInfo;

        switch ($this->reportInfo['period']['id']){
            case 1:
                $from = new DateTime('now');
                $to = new DateTime('now');

                $this->from = $from->setTime(0, 0);
                $this->to = $to->setTime(23, 59, 59);

                Log::alert('From: ' . json_encode($this->from));
                Log::alert('To: ' . json_encode($this->to));
                break;
            case 2:
                break;
        }
    }

    public function generate()
    {
        try {
            $campaigns = $this->project->campaigns;

            $data = [
                ['campaign_id', 'campaign', 'campaign_status', 'mailbox', 'sent'],
            ];

            foreach ($campaigns as $campaign) {
                $campaignStatisticService = new StatisticCampaignService($campaign);
                $sent = $campaignStatisticService->sentTime($this->from, $this->to);
                $data[] = [$campaign->id, $campaign->name, $campaign->status, $campaign->mailbox->email, $sent];
            }

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');

                foreach ($data as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };
            return $callback;
        } catch (Exception $error) {
            Log::error('ReportProjectService generate(): ' . $error->getMessage());
            return 0;
        }
    }
}
