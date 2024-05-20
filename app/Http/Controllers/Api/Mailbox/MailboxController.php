<?php

namespace App\Http\Controllers\Api\Mailbox;

use App\Http\Requests\Mailbox\MailboxConnectRequest;
use App\Http\Requests\Mailbox\MailboxUpdateRequest;
use App\Http\Requests\Mailbox\MailboxStoreRequest;
use App\Http\Resources\Mailbox\MailboxCreateResource;
use App\Http\Resources\Mailbox\MailboxResource;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\MailboxServices\GmailService;
use App\Services\MailboxServices\OutlookService;
use App\Services\MailboxServices\SMTPService;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use App\Models\Mailbox;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use PHPUnit\Framework\Error;

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
            $accountUuid = $request->user()->account->uuid;
            $connectedType = $request['connection_type'];
            $projectId = $request['project'];
            $mailboxService = match ($connectedType) {
                'gmail' => new GmailService(),
                'outlook' => new OutlookService(),
                'smtp' => new SMTPService(),
                default => throw new \Exception('Unknown connection type'),
            };
            $result = $mailboxService->connectAccount($accountUuid, $projectId);
            return $this->respondOk($result);
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        try {
            $queryStateString = request()->input('state');
            $queryStateJSON = json_decode($queryStateString, true);

            $driver = $queryStateJSON['driver'];
            $accountUuid = $queryStateJSON['account'];
            $projectUuid = $queryStateJSON['project'];

            $account = Account::query()->where('uuid', $accountUuid)->first();

            $user = Socialite::driver($driver)->stateless()->user();

            if (Mailbox::where('account_id', $account['id'])->where('email', $user->getEmail())->exists()) {
                return redirect()->to(env('FRONTEND_URL') . '/projects/' . $projectUuid . '/edit');
                // TODO rework;
            }

            $raw = $user->getRaw();

            $mailbox = Mailbox::create([
                "account_id" => $account['id'],
                "name" => $user->getName(),
                "email" => $user->getEmail(),
                "avatar" => $user->getAvatar(),
                "domain" => ($raw && array_key_exists("hd", $raw)) ? $raw["hd"] : "gmail",
                "token" => $user->token,
                "refresh_token" => $user->refreshToken,
                "expires_in" => $user->expiresIn,
                "email_provider" => 'gmail', //TODO rework it later
            ]);

            return redirect()->to(env('FRONTEND_URL') . '/projects/' . $projectUuid . '/edit');
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
