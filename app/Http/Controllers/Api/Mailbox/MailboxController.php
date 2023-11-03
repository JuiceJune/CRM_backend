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
use PHPUnit\Framework\Error;

class MailboxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $query = Mailbox::skip($offset)->take($limit);

            $mailboxes = $query->get();

            return response(MailboxResource::collection($mailboxes));
        } catch (Exception $error) {
            return response([
                "message" => "Problem with getting All Mailboxes",
                "error_message" => $error->getMessage(),
            ], 500);
        }
//        $user = Auth::user();
//
//        if ($user->isAdmin()) {
//            $mailboxes = Mailbox::all();
//        } else {
//            $mailboxes = $user->projects()->with('mailboxes')->get()->pluck('mailboxes')->flatten();
//        }
//        return response()->json(MailboxResource::collection($mailboxes));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // TODO add template for create mailboxes
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MailboxStoreRequest $request)
    {
       // TODO write store method
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
            return response([
                "message" => "Problem with getting Mailbox",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mailbox $mailbox)
    {
        try {
            return response(new MailboxResource($mailbox));
        } catch (Exception $error) {
            return response([
                "message" => "Problem with getting Mailbox for edit",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MailboxUpdateRequest $request, Mailbox $mailbox)
    {
        try {
            $validated = $request->validated();
            $mailbox->update($validated);
            return response('Mailbox updated successfully');
        } catch (Exception $error) {
            return response([
                "message" => "Problem with updating Mailbox",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mailbox $mailbox) {
        try {
            $mailbox->projects()->detach();
            $deletedMailbox = $mailbox->email;
            $mailbox->delete();

            return response($deletedMailbox);
        } catch (Exception $error) {
            return response([
                "message" => "Problem with destroying Mailbox",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }
}
