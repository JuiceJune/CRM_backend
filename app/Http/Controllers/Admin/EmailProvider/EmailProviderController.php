<?php

namespace App\Http\Controllers\Admin\EmailProvider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmailProvider\EmailProviderStoreRequest;
use App\Http\Requests\Admin\EmailProvider\EmailProviderUpdateRequest;
use App\Models\EmailProvider;
use Illuminate\Support\Facades\File;

class EmailProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $email_providers = EmailProvider::all();
        return view('admin.email-provider.index', compact('email_providers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.email-provider.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmailProviderStoreRequest $request)
    {
        $validated = $request->validated();
        if(isset($validated["logo"])) {
            $validated["logo"] = $request->file('logo')->store(
                'email-provider-logos', 'public'
            );
        } else {
            $validated["logo"] = "email-provider-logos/default.jpg";
        }
        $email_providers = EmailProvider::create($validated);
        return redirect()->route('admin.email-providers.index')->with('success', 'Email Provider created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $email_provider = EmailProvider::findOrFail($id);
        return view('admin.email-provider.show', compact('email_provider'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $email_provider = EmailProvider::findOrFail($id);
        return view('admin.email-provider.edit', compact('email_provider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmailProviderUpdateRequest $request, string $id)
    {
        $validated = $request->validated();
        $email_provider = EmailProvider::findOrFail($id);
        if(isset($validated["logo"])) {
            $validated["logo"] = $request->file('logo')->store(
                'email-provider-logos', 'public'
            );
            if(File::exists(public_path('storage/'.$email_provider->logo)) && $email_provider->logo != "email-provider-logos/default.jpg") {
                File::delete(public_path('storage/'.$email_provider->logo));
            }
        }
        $email_provider->update($validated);
        return redirect()->route('admin.email-providers.index')->with('success', 'Email Provider updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $email_provider = EmailProvider::find($id);
            if($email_provider->delete()) {
                return redirect()->route('admin.email-providers.index')->with('success', 'Email Provider deleted successfully.');
            } else {
                return redirect()->route('admin.email-providers.index')->with('error', 'Email Provider not deleted.');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.email-providers.index')->with('error', 'This email provider cannot be deleted due to existing dependencies (mailbox).');
        }
    }
}
