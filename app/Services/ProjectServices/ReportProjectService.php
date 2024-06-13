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

        switch ($reportInfo['period']['id']){
            case 1:
            default:
                $from = new DateTime('now');
                $to = new DateTime('now');

                $this->from = $from->setTime(0, 0);
                $this->to = $to->setTime(23, 59, 59);
                break;

            case 2: // Вчора
                $from = new DateTime('yesterday');
                $to = new DateTime('yesterday');
                $this->from = $from->setTime(0, 0);
                $this->to = $to->setTime(23, 59, 59);
                break;

            case 3: // Останні 7 днів
                $from = new DateTime('now');
                $to = new DateTime('now');
                $this->from = $from->modify('-6 days')->setTime(0, 0);
                $this->to = $to->setTime(23, 59, 59);
                break;

            case 4: // Останні 30 днів
                $from = new DateTime('now');
                $to = new DateTime('now');
                $this->from = $from->modify('-29 days')->setTime(0, 0);
                $this->to = $to->setTime(23, 59, 59);
                break;

            case 5: // custom range
                $this->from = new DateTime($reportInfo['from']);
                $this->to = new DateTime($reportInfo['to']);
                break;
        }

        Log::alert('From: ' . json_encode($this->from));
        Log::alert('To: ' . json_encode($this->to));
    }

    public function generate()
    {
        try {
            $campaigns = $this->project->campaigns;

            $data = [
                [
                    'campaign_id', 'campaign', 'campaign_status', 'mailbox', 'contacted_prospects',
                    'bounced', 'bounced_sent', 'opened', 'opened_rete', 'clicked', 'opt_out', 'delivered',
                    'responded', 'responded_rate', 'interested_yes', 'interested_maybe', 'interested_no'
                ],
            ];

            foreach ($campaigns as $campaign) {
                $campaignStatisticService = new StatisticCampaignService($campaign);

                $sent = $campaignStatisticService->sentTime($this->from, $this->to);
                $delivered = $campaignStatisticService->deliveredTime($this->from, $this->to);
                $opened = $campaignStatisticService->openedTime($this->from, $this->to);
                $responded = $campaignStatisticService->respondedTime($this->from, $this->to);
                $bounced = $campaignStatisticService->bouncedTime($this->from, $this->to);

                $bouncedRate = $sent > 0 ? ($bounced * 100 / $sent) : 0;
                $openedRate = $delivered > 0 ? ($opened * 100 / $delivered) : 0;
                $respondedRate = $delivered > 0 ? ($responded * 100 / $delivered) : 0;

                $data[] = [
                    $campaign->id,
                    $campaign->name,
                    $campaign->status,
                    $campaign->mailbox->email ?? null,
                    $sent,
                    $bounced,
                    $bouncedRate,
                    $opened,
                    $openedRate,
                    0,
                    $bounced,
                    $delivered,
                    $responded,
                    $respondedRate,
                    0,
                    0,
                    0
                ];
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
