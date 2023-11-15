<?php

namespace App\Http\Controllers\Api\Campaign;

use App\Events\CampaignStarted;
use App\Events\CampaignStopped;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Campaign\CampaignStoreRequest;
use App\Http\Requests\Admin\Campaign\CampaignUpdateRequest;
use App\Http\Resources\CampaignResource;
use App\Jobs\MailJob;
use App\Jobs\SetupCampaign;
use App\Models\Mailbox;
use App\Models\Campaign;
use App\Models\Project;
use Carbon\Carbon;
use DateTimeZone;
use Google\Service\Gmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $query = Campaign::skip($offset)->take($limit);

            $campaigns = $query->get();

            return response(CampaignResource::collection($campaigns));
        } catch (Exception $error) {
            return response([
                "message" => "Problem with getting All Campaigns",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $project_id = $request->input('project_id');
            $timezones = DateTimeZone::listIdentifiers();
            if ($project_id) {
                $projects = Project::find($project_id);
                $mailboxes = $projects->mailboxes;
                return response(["mailboxes" => $mailboxes, 'timezones' => $timezones]);
            } else {
                $mailboxes = Mailbox::select('id', 'email', 'name')->get();
                $projects = Project::select('id', 'name')->get();
                return response(["mailboxes" => $mailboxes, "projects" => $projects, 'timezones' => $timezones]);
            }
        } catch (Exception $error) {
            return response([
                "message" => "Problem with getting info for creating Campaign",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CampaignStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['sending_time_json'] = json_decode($validated['sending_time_json']);
            $campaign = Campaign::create($validated);

            return response($campaign['id']);
        } catch (Exception $error) {
            return response([
                "message" => "Problem with store Project",
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
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        try {
            $project = $campaign->project;
            $mailboxes = $project->mailboxes;
            $timezones = DateTimeZone::listIdentifiers();
            return response(["campaign" => $campaign, "mailboxes" => $mailboxes, 'timezones' => $timezones]);
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CampaignUpdateRequest $request, Campaign $campaign)
    {
        try {
            $validated = $request->validated();
            $validated['sending_time_json'] = json_decode($validated['sending_time_json']);
            $campaign->update($validated);
            return response('Campaign successfully updated');
        } catch (Exception $error) {
            return response([
                "message" => "Problem with updating Project",
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

    /**
     * Send test email.
     */
    public function sendTestEmail(Request $request)
    {
        try {
            $mailbox_id = $request->input('mailbox_id');
            $message = $request->input('message');
            $subject = $request->input('subject');
            $test_email = $request->input('test_email');
            $snippets = $request->input('snippets');
            $mailbox = Mailbox::find($mailbox_id);
            if ($mailbox) {
                $messageText = $message;
                if (count($snippets) > 0) {
                    foreach ($snippets as $key => $snippet) {
                        $key = strtoupper($key);
                        $messageText = str_replace('{{' . $key . '}}', $snippet, $messageText);
                        $subject = str_replace('{{' . $key . '}}', $snippet, $subject);
                    }
                }
                $client = (new \App\Http\Controllers\Api\Google\GoogleController)->getGoogleClient($mailbox["token"]);
                $sender_name = $mailbox['name'];
                $sender_email = $mailbox['email'];
                $signature = $mailbox['signature'];
                $recipient = $test_email; // Адреса отримувача
                $service = new Gmail($client);
                $message = (new \App\Http\Controllers\Api\Google\GoogleController)->createMessage($sender_name, $sender_email, $recipient, $subject, $messageText, $signature);
                $response = $service->users_messages->send('me', $message);
                return response('Email send successfully');
            } else {
                return response('Mailbox not found');
            }
        } catch (Exception $error) {
            return response([
                "message" => "Problem with sending test message",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    public function showCampaignQueue(Campaign $campaign)
    {
        try {
            $jobs = DB::table('jobs')->get();
//            $jobs = DB::table('jobs')->where('queue', 'campaign_' . $campaign->id)->get();
            foreach ($jobs as $job) {
                $job->available_at = Carbon::createFromTimestamp($job->available_at);
            }
            return response($jobs);
        } catch (Exception $error) {
            return response([
                "message" => "Problem with showing campaign queue",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    public function startCampaign(Campaign $campaign)
    {
        try {
            SetupCampaign::dispatch($campaign);
            $campaign->update(['status' => 'started']);
            return response($campaign);
        } catch (Exception $error) {
            return response([
                "message" => "Problem with starting campaign",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    public function stopCampaign(Campaign $campaign)
    {
        try {
//            event(new CampaignStopped($campaign));
            $campaign->update(['status' => 'stopped']);
            return response($campaign);
        } catch (Exception $error) {
            return response([
                "message" => "Problem with stopping campaign",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    public function clearQueue()
    {
        try {
            DB::table('jobs')->delete();
            return response('Queue are cleared');
        } catch (Exception $error) {
            return response([
                "message" => "Problem with clearing queue",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }
}
