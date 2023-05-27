<?php

namespace App\Http\Controllers\Admin\Linkedin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Linkedin\LinkedinStoreRequest;
use App\Http\Requests\Admin\Linkedin\LinkedinUpdateRequest;
use App\Models\Linkedin;
use App\Models\Mailbox;
use Illuminate\Support\Facades\File;

class LinkedinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $linkedin_accounts = Linkedin::all();
        return view('admin.linkedin.index', compact('linkedin_accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mailboxes = Mailbox::all();
        return view('admin.linkedin.create', compact('mailboxes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LinkedinStoreRequest $request)
    {
        $validated = $request->validated();

        $validated["warmup"] = isset($validated["warmup"]);

        if(isset($validated["avatar"])) {
            $validated["avatar"] = $request->file('avatar')->store(
                'linkedin_accounts/avatars', 'public'
            );
        } else {
            $validated["avatar"] = "linkedin_accounts/avatars/default.png";
        }

        Linkedin::create($validated);

        return redirect()->route('admin.linkedin-accounts.index')->with('success', 'Linkedin created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $linkedin = Linkedin::findOrFail($id);
        return view('admin.linkedin.show', compact('linkedin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $linkedin = Linkedin::findOrFail($id);
        $mailboxes = Mailbox::all();
        return view('admin.linkedin.edit', compact('linkedin','mailboxes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LinkedinUpdateRequest $request, string $id)
    {
        //TODO add services
        $validated = $request->validated();

        $linkedin = Linkedin::findOrFail($id);

        $validated["warmup"] = isset($validated["warmup"]);

        if(isset($validated["avatar"])) {
            $validated["avatar"] = $request->file('avatar')->store(
                'linkedin_accounts/avatars', 'public'
            );
            if(File::exists(public_path('storage/'.$linkedin->avatar)) && $linkedin->avatar != "linkedin_accounts/avatars/default.png") {
                File::delete(public_path('storage/'.$linkedin->avatar));
            }
        }
        $linkedin->update($validated);

        return redirect()->route('admin.linkedin-accounts.index')->with('success', 'Linkedin updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            //TODO delete avatar file
            $linkedin = Linkedin::find($id);
            $linkedin->projects()->detach();
            if($linkedin->delete()) {
                return redirect()->route('admin.linkedin-accounts.index')->with('success', 'Linkedin deleted successfully.');
            } else {
                return redirect()->route('admin.linkedin-accounts.index')->with('error', 'Linkedin not deleted.');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.mailboxes.index')->with('error', 'This linkedin cannot be deleted due to existing dependencies');
        }
    }
}
