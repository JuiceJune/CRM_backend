<?php

namespace App\Http\Controllers\Api\Activity;

use App\Http\Controllers\Controller;
use App\Http\Resources\Campaign\CampaignResource;
use App\Models\Campaign;
use Illuminate\Http\Request;
use PHPUnit\Exception;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $activities = Activity::all();
            return response($activities);
        } catch (Exception $error) {
            return response([
                "message" => "Problem with getting All Activities",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        try {
            $currentCampaign = new CampaignResource($campaign);
            return response($currentCampaign);
        } catch (Exception $error) {
            return response([
                "message" => "Problem with getting Campaign",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $campaign = Campaign::find($id);
            $campaign->delete();
            return response('Campaign deleted successfully');
        } catch (Exception $error) {
            return response([
                "message" => "Problem with destroying Campaign",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }
}
