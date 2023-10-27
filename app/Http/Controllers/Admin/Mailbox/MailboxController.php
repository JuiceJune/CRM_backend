<?php

namespace App\Http\Controllers\Admin\Mailbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Mailbox\MailboxStoreRequest;
use App\Http\Requests\Admin\Mailbox\MailboxUpdateRequest;
use App\Models\Mailbox;
use App\Models\Project;
use Illuminate\Support\Facades\File;

class MailboxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mailboxes = Mailbox::all();
        return view('admin.mailbox.index', compact('mailboxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.mailbox.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MailboxStoreRequest $request)
    {
        $validated = $request->validated();

        if(isset($validated["avatar"])) {
            $validated["avatar"] = $request->file('avatar')->store(
                'mailboxes/avatars', 'public'
            );
        } else {
            $validated["avatar"] = "mailboxes/avatars/default.png";
        }

        Mailbox::create($validated);

        return redirect()->route('admin.mailboxes.index')->with('success', 'Mailbox created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mailbox = Mailbox::findOrFail($id);
        return view('admin.mailbox.show', compact('mailbox'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $mailbox = Mailbox::findOrFail($id);
        return view('admin.mailbox.edit', compact('mailbox'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MailboxUpdateRequest $request, string $id)
    {
        //TODO add services
        $validated = $request->validated();

        $mailbox = Mailbox::findOrFail($id);

        if(isset($validated["avatar"])) {
            $validated["avatar"] = $request->file('avatar')->store(
                'mailboxes/avatars', 'public'
            );
            if(File::exists(public_path('storage/'.$mailbox->avatar)) && $mailbox->avatar != "mailboxes/avatars/default.png") {
                File::delete(public_path('storage/'.$mailbox->avatar));
            }
        }
        $mailbox->update($validated);

        return redirect()->route('admin.mailboxes.index')->with('success', 'Mailbox updated successfully.');
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
