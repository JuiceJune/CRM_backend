<?php

namespace App\Services\CampaignServices;

use Illuminate\Support\Facades\Log;
use App\Models\Campaign;
use Exception;

class ReportCampaignService {

    public Campaign $campaign;
    public array $reportInfo;
    public function __construct(Campaign $campaign, array $reportInfo)
    {
        $this->campaign = $campaign;
        $this->reportInfo = $reportInfo;
    }

    public function generate()
    {
        try {
            $data = [
                ['Name', 'Email', 'Age'],
                ['John Doe', 'john@example.com', 25],
                ['Jane Doe', 'jane@example.com', 28],
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
            Log::error('ReportCampaignService generate(): ' . $error->getMessage());
            return 0;
        }
    }
}
