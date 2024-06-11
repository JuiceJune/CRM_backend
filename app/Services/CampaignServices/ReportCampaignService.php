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

    public function generate(): int|string
    {
        try {
            $data = [
                ['Name', 'Email', 'Age'],
                ['John Doe', 'john@example.com', 25],
                ['Jane Doe', 'jane@example.com', 28],
            ];

            $fileName = 'report.csv';

            $file = fopen(storage_path('app/public/' . $fileName), 'w');

            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);

            return $file;
        } catch (Exception $error) {
            Log::error('ReportCampaignService generate(): ' . $error->getMessage());
            return 0;
        }
    }
}
