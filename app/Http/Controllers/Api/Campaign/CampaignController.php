<?php

namespace App\Http\Controllers\Api\Campaign;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CampaignStoreRequest;
use App\Http\Requests\Campaign\CampaignUpdateRequest;
use App\Http\Resources\Campaign\CampaignEditResource;
use App\Http\Resources\Campaign\CampaignShowResource;
use App\Http\Resources\Campaign\CampaignsTableInProjectResource;
use App\Http\Resources\Mailbox\MailboxCampaignCreateResource;
use App\Jobs\SetupCampaignJob;
use App\Jobs\StopCampaignJob;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\CampaignStep;
use App\Models\CampaignStepVersion;
use App\Models\Mailbox;
use App\Models\Project;
use App\Services\CampaignMessageService\CampaignMessageService;
use App\Services\CampaignServices\ReportCampaignService;
use App\Services\MailboxServices\GmailService;
use Carbon\Carbon;
use DateTimeZone;
use Exception;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            $my = $request->input('my', false);

            $userId = auth()->id();

            $query = Campaign::query();

            if ($my) {
                $query = $query->whereHas('project', function ($q) use ($userId) {
                    $q->where('creator_id', $userId);
                });
            }

            $query = $query->skip($offset)->take($limit);

            $campaigns = $query->get();

            return response()->json(CampaignsTableInProjectResource::collection($campaigns));
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
            return $this->respondWithSuccess(new CampaignShowResource($campaign));
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

            if(isset($validated['start_date']))
                $validated['start_date'] = Carbon::parse($validated['start_date']);

            $campaign->update($validated);

            if (!empty($validated['steps'])) {
                $stepIds = collect($validated['steps'])->pluck('id')->filter();
                $campaign->steps()->whereNotIn('uuid', $stepIds)->delete();

                foreach ($validated['steps'] as $stepData) {
                    $stepData['account_id'] = $validated['account_id'];
                    $step = $campaign->steps()->updateOrCreate(['uuid' => $stepData['id'] ?? null], $stepData);

                    if (!empty($stepData['versions'])) {
                        $versionIds = collect($stepData['versions'])->pluck('id')->filter();
                        $step->versions()->whereNotIn('uuid', $versionIds)->delete();

                        foreach ($stepData['versions'] as $versionData) {
                            $versionData['account_id'] = $validated['account_id'];
                            $step->versions()->updateOrCreate(['uuid' => $versionData['id'] ?? null], $versionData);
                        }
                    }
                }
            }

            DB::commit();
            return $this->respondWithSuccess(new CampaignEditResource($campaign));
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

    /**
     * Send test email.
     */
    public function sendTestEmail(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $mailbox_id = $request->input('mailbox_id');
            $message = $request->input('message');
            $subject = $request->input('subject');
            $test_email = $request->input('test_email');
            $snippets = $request->input('snippets');

            $mailbox = Str::isUuid($mailbox_id) ? Mailbox::where('uuid', $mailbox_id)->first() : Mailbox::find($mailbox_id);
            if ($mailbox) {
                $gmailService = new GmailService();
                $res = $gmailService->sendTestMessage($mailbox, $message, $subject, $test_email, $snippets);

                if($res['status'] === 'success') {
                    return $this->respondOk('Email send successfully');
                } else {
                    throw new \Error($res['data']);
                }
            } else {
                throw new \Error('Mailbox not found');
            }
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    public function startCampaign(Campaign $campaign): \Illuminate\Http\JsonResponse
    {
        try {
            if(!$campaign->mailbox) {
                throw new \Error('Mailbox is not define');
            }
            SetupCampaignJob::dispatch($campaign);

            $campaign->update(['status' => 'started']);
            return $this->respondOk($campaign->name);
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    public function stopCampaign(Campaign $campaign): \Illuminate\Http\JsonResponse
    {
        try {
            StopCampaignJob::dispatch($campaign);

            $campaign->update(['status' => 'stopped']);
            return $this->respondOk($campaign->name);
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    public function openEmail(CampaignMessage $campaignMessage, Request $request)
    {
        try {
            if(!in_array($campaignMessage['status'], ['unsubscribe', 'bounced', 'responded', 'opened'])) {
                $campaignMessageService = new CampaignMessageService($campaignMessage);

                $campaign = $campaignMessage->campaign;
                $project = $campaign->project;
                $creator = $project->creator;

                $members = $project->users;
                $ipAddress = $request->ip();

                $duplicateIpFound = false;
                foreach ($members as $member) {
                    if ($member->ip === $ipAddress) {
                        $duplicateIpFound = true;
                        break;
                    }
                }

                if (!$duplicateIpFound && $creator->ip !== $ipAddress) {
                    $campaignMessageService->opened($ipAddress);
                }
            }
        } catch (Exception $error) {
            Log::channel('dev-campaign-process')->error('OpenEmail: ' . $error->getMessage());
        } finally {
            $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
            return response($pixel)->header('Content-Type', 'image/gif');
        }
    }

    public function unsubscribe(CampaignMessage $campaignMessage, Request $request): void
    {
        try {
            if(!in_array($campaignMessage['status'], ['unsubscribe', 'bounced'])) {
                $campaignMessageService = new CampaignMessageService($campaignMessage);
                $campaignMessageService->unsubscribe($request->ip());
            }
        } catch (Exception $error) {
            Log::channel('dev-campaign-process')->error('Unsubscribe: ' . $error->getMessage());
        }
    }

    public function generateReport(Campaign $campaign, Request $request)
    {
        try {
            $reportInfo = $request->input('periodInfo');
            Log::channel('dev-campaign-process')->alert('$reportInfo: ' . json_encode($reportInfo));

            $reportGenerator = new ReportCampaignService($campaign, $reportInfo);
            $callback = $reportGenerator->generate();

            if ($callback) {
                return response()->stream($callback, 200, [
                    'Content-Type' => 'text/csv',
                    'X-File-Name' => 'report1.csv'
                ]);
            } else {
                throw new \Error('No report found');
            }
        } catch (\Exception $error) {
            Log::channel('dev-campaign-process')->error('generateReport: ' . $error->getMessage());
            return $this->respondError($error->getMessage());
        }
    }

}
