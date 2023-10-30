<?php

namespace App\Http\Controllers\Api\Campaign;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Campaign\CampaignStoreRequest;
use App\Http\Requests\Admin\Campaign\CampaignUpdateRequest;
use App\Http\Resources\CampaignResource;
use App\Models\Mailbox;
use App\Models\Campaign;
use App\Models\Project;
use Google\Service\Gmail;
use Illuminate\Http\Request;
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
            return response($error, 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $project_id = $request->input('project_id');
            if ($project_id) {
                $projects = Project::find($project_id);
                $mailboxes = $projects->mailboxes;
                return response(["mailboxes" => $mailboxes]);
            } else {
                $mailboxes = Mailbox::select('id', 'email', 'name')->get();
                $projects = Project::select('id', 'name')->get();
                return response(["mailboxes" => $mailboxes, "projects" => $projects]);
            }
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CampaignStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $campaign = Campaign::create($validated);
            return response($campaign);
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        try {
            $currentCampaign = new CampaignResource($campaign);
            return response()->json($currentCampaign);
        } catch (Exception $error) {
            return response($error, 400);
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
            return response(["campaign" => $campaign, "mailboxes" => $mailboxes]);
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
            $campaign->update($validated);
            return response('Campaign successfully updated');
        } catch (Exception $error) {
            return response($error, 400);
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
            return response($error, 400);
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
            $mailbox = Mailbox::find($mailbox_id);
            if ($mailbox) {
                $client = (new \App\Http\Controllers\Api\Google\GoogleController)->getGoogleClient($mailbox["token"]);
                $sender_name = $mailbox['name'];
                $sender_email = $mailbox['email'];
                $signature = $mailbox['signature'];
                $recipient = $test_email; // Адреса отримувача
                $messageText = $message;
                $service = new Gmail($client);
                $message = (new \App\Http\Controllers\Api\Google\GoogleController)->createMessage($sender_name, $sender_email, $recipient, $subject, $messageText, $signature);
                $response = $service->users_messages->send('me', $message);
                return response()->json([
                    "message" => 'Email send successfully',
                    "response" => $response
                ]);
            } else {
                return response('Mailbox not found');
            }
        } catch (Exception $error) {
            return response($error, 400);
        }
    }
}
