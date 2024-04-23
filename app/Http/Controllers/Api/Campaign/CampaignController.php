<?php

namespace App\Http\Controllers\Api\Campaign;

use App\Events\CampaignStopped;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CampaignStoreRequest;
use App\Http\Requests\Campaign\CampaignUpdateRequest;
use App\Http\Resources\Campaign\CampaignEditResource;
use App\Http\Resources\Campaign\CampaignResource;
use App\Http\Resources\EmailJobResource;
use App\Http\Resources\Mailbox\MailboxCampaignCreateResource;
use App\Http\Resources\Mailbox\MailboxCreateResource;
use App\Http\Resources\Mailbox\MailboxResource;
use App\Jobs\SetupCampaign;
use App\Jobs\SetupCampaignJob;
use App\Jobs\StopCampaignJob;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\CampaignProspect;
use App\Models\CampaignSentProspect;
use App\Models\CampaignStep;
use App\Models\CampaignStepProspect;
use App\Models\CampaignStepVersion;
use App\Models\EmailJob;
use App\Models\Mailbox;
use App\Models\Project;
use App\Services\CampaignMessageService\CampaignMessageService;
use Carbon\Carbon;
use DateTimeZone;
use Exception;
use F9Web\ApiResponseHelpers;
use Google\Service\Gmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $limit = $request->input('limit', 50);
            $offset = $request->input('offset', 0);

            $query = Campaign::query()->skip($offset)->take($limit);

            $campaigns = $query->get();

            return response()->json(CampaignResource::collection($campaigns));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $project_id = $request->input('project_id');

            $timezones = DateTimeZone::listIdentifiers();
            $project = Project::query()->where('uuid', $project_id)->first();
            $mailboxes = MailboxCampaignCreateResource::collection($project->mailboxes);

            $snippets = DB::getSchemaBuilder()->getColumnListing('prospects');
            $columnsToExclude = ['created_at', 'date_contacted', "date_added", "date_responded", "id", "status", "tags", "timezone", "updated_at"];
            $filteredSnippets = array_filter($snippets, function ($column) use ($columnsToExclude) {
                return !in_array($column, $columnsToExclude);
            });

            return $this->respondWithSuccess([
                "mailboxes" => $mailboxes,
                'timezones' => $timezones,
                'snippets' => $filteredSnippets,
                'project_id' => $project->id
            ]);
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CampaignStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $user = $request->user();
            $validated['account_id'] = $user->account_id;

            $validated['priority_config'] = [
                [100],
                [20, 80],
                [20, 40, 40],
                [20, 26, 26, 26],
                [20, 20, 20, 20, 20],
                [20, 16, 16, 16, 16, 16],
                [20, 11, 11, 11, 11, 11, 11],
                [20, 10, 10, 10, 10, 10, 10, 10],
                [20, 9, 9, 9, 9, 9, 9, 9, 9],
            ];

            $validated['start_date'] = Carbon::parse($validated['start_date']);

            $campaign = Campaign::create($validated);

            foreach ($validated['steps'] as $step) {
                $step['campaign_id'] = $campaign['id'];
                $step['account_id'] = $validated['account_id'];
                $campaignStep = CampaignStep::create($step);

                foreach ($step['versions'] as $version) {
                    $version['campaign_step_id'] = $campaignStep['id'];
                    $version['account_id'] = $validated['account_id'];
                    CampaignStepVersion::create($version);
                }
            }
            DB::commit();

            return $this->respondOk($campaign['uuid']);
        } catch (\Exception $error) {
            DB::rollBack();
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->respondWithSuccess(new CampaignResource($campaign));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign): \Illuminate\Http\JsonResponse
    {
        try {
            $project = $campaign->project;
            $mailboxes = MailboxCampaignCreateResource::collection($project->mailboxes);
            $timezones = DateTimeZone::listIdentifiers();

            $snippets = DB::getSchemaBuilder()->getColumnListing('prospects');
            $columnsToExclude = ['created_at', 'date_contacted', "date_added", "date_responded", "id", "status", "tags", "timezone", "updated_at"]; // Вкажіть назви колонок, які вам не потрібні
            $filteredSnippets = array_filter($snippets, function ($column) use ($columnsToExclude) {
                return !in_array($column, $columnsToExclude);
            });

            return $this->respondWithSuccess([
                "campaign" => new CampaignEditResource($campaign),
                "mailboxes" => $mailboxes,
                'timezones' => $timezones,
                'snippets' => $filteredSnippets
            ]);
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CampaignUpdateRequest $request, Campaign $campaign): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $user = $request->user();
            $validated['account_id'] = $user->account_id;

            $validated['priority_config'] = [
                [100],
                [20, 80],
                [20, 40, 40],
                [20, 26, 26, 26],
                [20, 20, 20, 20, 20],
                [20, 16, 16, 16, 16, 16],
                [20, 11, 11, 11, 11, 11, 11],
                [20, 10, 10, 10, 10, 10, 10, 10],
                [20, 9, 9, 9, 9, 9, 9, 9, 9],
            ];

            $validated['start_date'] = Carbon::parse($validated['start_date']);

            $campaign->update($validated);

            $stepIds = collect($validated['steps'])->pluck('id')->filter();

            $campaign->steps()->whereNotIn('uuid', $stepIds)->delete();

            foreach ($validated['steps'] as $stepData) {
                $stepData['account_id'] = $validated['account_id'];
                $step = $campaign->steps()->updateOrCreate(['uuid' => $stepData['id'] ?? null], $stepData);

                $versionIds = collect($stepData['versions'])->pluck('id')->filter();

                $step->versions()->whereNotIn('uuid', $versionIds)->delete();

                foreach ($stepData['versions'] as $versionData) {
                    $versionData['account_id'] = $validated['account_id'];
                    $step->versions()->updateOrCreate(['uuid' => $versionData['id'] ?? null], $versionData);
                }
            }

            DB::commit();

            return $this->respondOk("Campaign updated successfully");
        } catch (\Exception $error) {
            DB::rollBack();
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign): \Illuminate\Http\JsonResponse
    {
        try {
            $campaign->delete();
            return $this->respondOk($campaign->name);
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    //TODO create services for all these staff

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
            Log::channel('development')->error('Snippets: ' . json_encode($snippets));

            $mailbox = Mailbox::find($mailbox_id);
            if ($mailbox) {
                $messageText = $message;
                if (count($snippets) > 0) {
                    foreach ($snippets as $key => $snippet) {
                        $messageText = str_replace('{{' . $key . '}}', $snippet, $messageText);
                        $subject = str_replace('{{' . $key . '}}', $snippet, $subject);
                    }
                }
                $client = (new \App\Http\Controllers\Api\Google\GoogleController)->getClient($mailbox["token"]);
                $sender_name = $mailbox['name'];
                $sender_email = $mailbox['email'];
                $signature = str_replace('{{UNSUBSCRIBE}}', '#', $mailbox['signature']);;
                $recipient = $test_email; // Адреса отримувача
                $service = new Gmail($client);
                $message = (new \App\Http\Controllers\Api\Google\GoogleController)->createMessage($sender_name, $sender_email, $recipient, $subject, $messageText, $signature, null);
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
            $jobs = EmailJobResource::collection(EmailJob::all());
            return response($jobs);
        } catch (Exception $error) {
            return response([
                "message" => "Problem with showing campaign queue",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    public function startCampaign(Campaign $campaign): \Illuminate\Http\JsonResponse
    {
        try {
            SetupCampaignJob::dispatch($campaign);
            $campaign->update(['status' => 'started']);
            return response()->json(new CampaignResource($campaign));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    public function stopCampaign(Campaign $campaign): \Illuminate\Http\JsonResponse
    {
        try {
            StopCampaignJob::dispatch($campaign);
            $campaign->update(['status' => 'stopped']);
            return response()->json(new CampaignResource($campaign));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    public function openEmail(CampaignMessage $campaignMessage)
    {
        try {
            if(!in_array($campaignMessage['status'], ['unsubscribe', 'bounced', 'replayed', 'opened'])) {
                $campaignMessageService = new CampaignMessageService($campaignMessage);
                $campaignMessageService->opened();
            }
        } catch (Exception $error) {
            Log::error('OpenEmail: ' . $error->getMessage());
        } finally {
            $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
            return response($pixel)->header('Content-Type', 'image/gif');
        }
    }

    public function unsubscribe(CampaignMessage $campaignMessage): void
    {
        try {
            if(!in_array($campaignMessage['status'], ['unsubscribe', 'bounced'])) {
                $campaignMessageService = new CampaignMessageService($campaignMessage);
                $campaignMessageService->unsubscribe();
            }
        } catch (Exception $error) {
            Log::error('Unsubscribe: ' . $error->getMessage());
        }
    }
}
