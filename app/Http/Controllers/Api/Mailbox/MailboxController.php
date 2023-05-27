<?php

namespace App\Http\Controllers\Api\Mailbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Mailbox\MailboxStoreRequest;
use App\Http\Requests\Admin\Mailbox\MailboxUpdateRequest;
use App\Http\Resources\MailboxResource;
use App\Http\Resources\UserResource;
use App\Models\EmailProvider;
use App\Models\Mailbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

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
        $email_providers = EmailProvider::all();
        return response()->json([
            "email_providers" => $email_providers,
        ]);
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
        return new MailboxResource($mailbox);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mailbox $mailbox)
    {
        $email_providers = EmailProvider::all();
        return response()->json([
            "mailbox" => new MailboxResource($mailbox),
            "email_providers" => $email_providers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MailboxUpdateRequest $request, Mailbox $mailbox)
    {
        //TODO add services
        $validated = $request->validated();

//        if(isset($validated["avatar"])) {
//            $validated["avatar"] = $request->file('avatar')->store(
//                'mailboxes/avatars', 'public'
//            );
//            if(File::exists(public_path('storage/'.$mailbox->avatar)) && $mailbox->avatar != "mailboxes/avatars/default.png") {
//                File::delete(public_path('storage/'.$mailbox->avatar));
//            }
//        }
        $mailbox->update($validated);

        return new MailboxResource($mailbox);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            //TODO delete avatar file
            $mailbox = Mailbox::find($id);
            $mailbox->projects()->detach();
            if($mailbox->delete()) {
                return redirect()->route('admin.mailboxes.index')->with('success', 'Mailbox deleted successfully.');
            } else {
                return redirect()->route('admin.mailboxes.index')->with('error', 'Mailbox not deleted.');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.mailboxes.index')->with('error', 'This mailbox cannot be deleted due to existing dependencies (linkedin account)');
        }
    }
}
