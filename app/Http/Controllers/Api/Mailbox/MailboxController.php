<?php

namespace App\Http\Controllers\Api\Mailbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Mailbox\MailboxStoreRequest;
use App\Http\Requests\Admin\Mailbox\MailboxUpdateRequest;
use App\Http\Resources\MailboxResource;
use App\Models\Mailbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Exception;

class MailboxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $mailboxes = Mailbox::all();
        } else {
            $mailboxes = $user->projects()->with('mailboxes')->get()->pluck('mailboxes')->flatten();
        }
        return response()->json(MailboxResource::collection($mailboxes));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
//        return response()->json([
//            "email_providers" => $email_providers,
//        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MailboxStoreRequest $request)
    {
        $validated = $request->validated();

        $validated["for_linkedin"] = isset($validated["for_linkedin"]);

        if(isset($validated["avatar"])) {
            $validated["avatar"] = $request->file('avatar')->store(
                'mailboxes/avatars', 'public'
            );
        } else {
            $validated["avatar"] = "mailboxes/avatars/default.png";
        }

        $mailbox = Mailbox::create($validated);

        return response(new MailboxResource($mailbox), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Mailbox $mailbox)
    {
        try {
            $currentMailbox = new MailboxResource($mailbox);
            return response($currentMailbox);
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mailbox $mailbox)
    {
        return response()->json([
            "mailbox" => new MailboxResource($mailbox),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MailboxUpdateRequest $request, Mailbox $mailbox)
    {
        try {
            //TODO add services
            $validated = $request->validated();
            $mailbox->update($validated);
            return response($mailbox);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Error update mailbox: ' . $error->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mailbox $mailbox) {
        try {
            if(!$mailbox) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mailbox ' . $mailbox->email . ' not found'
                ], 404);
            }

            $mailbox->projects()->detach();

            $mailbox->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mailbox ' . $mailbox->email . ' deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting mailbox: ' . $e->getMessage()
            ], 500);
        }
    }
}
