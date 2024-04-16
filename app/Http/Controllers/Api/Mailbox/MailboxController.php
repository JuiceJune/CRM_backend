<?php

namespace App\Http\Controllers\Api\Mailbox;

use App\Http\Requests\Mailbox\MailboxConnectRequest;
use App\Http\Requests\Mailbox\MailboxUpdateRequest;
use App\Http\Requests\Mailbox\MailboxStoreRequest;
use App\Http\Resources\Mailbox\MailboxCreateResource;
use App\Http\Resources\Mailbox\MailboxResource;
use App\Http\Controllers\Controller;
use App\Services\Mailbox\GmailService;
use App\Services\Mailbox\OutlookService;
use App\Services\Mailbox\SMTPService;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use App\Models\Mailbox;

class MailboxController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $query = Mailbox::query()->skip($offset)->take($limit);

            $mailboxes = $query->get();

            return response()->json(MailboxResource::collection($mailboxes));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function connect(MailboxConnectRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $connectedType = $request['connection_type'];
            $mailboxService = match ($connectedType) {
                'gmail' => new GmailService(),
                'outlook' => new OutlookService(),
                'smtp' => new SMTPService(),
                default => throw new \Exception('Unknown connection type'),
            };
            $result = $mailboxService->connect();
            return $this->respondOk($result);
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MailboxStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = $request->user();
            $validated['account_id'] = $user->account_id;

            $mailbox = Mailbox::create($validated);
            return $this->respondCreated(new MailboxResource($mailbox));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Mailbox $mailbox): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->respondWithSuccess(new MailboxResource($mailbox));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MailboxUpdateRequest $request, Mailbox $mailbox): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();
            $mailbox->update($validated);
            return $this->respondWithSuccess(new MailboxCreateResource($mailbox));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mailbox $mailbox): \Illuminate\Http\JsonResponse
    {
        try {
            $mailbox->delete();
            return $this->respondOk($mailbox->email);
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }
}
