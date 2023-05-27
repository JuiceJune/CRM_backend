<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Client\ClientStoreRequest;
use App\Http\Requests\Admin\Client\ClientUpdateRequest;
use App\Models\Client;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::all();
        return view('admin.client.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.client.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientStoreRequest $request)
    {
        $validated = $request->validated();

        if (isset($validated["logo"])) {
            $validated["logo"] = $request->file('logo')->store(
                'clients/logos', 'public'
            );
        } else {
            $validated["avatar"] = "clients/logos/default.png";
        }

        Client::create($validated);
        return redirect()->route('admin.clients.index')->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $client = Client::findOrFail($id);
        return view('admin.client.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $client = Client::findOrFail($id);
        return view('admin.client.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientUpdateRequest $request, string $id)
    {
        $validated = $request->validated();

        $client = Client::findOrFail($id);

        if (isset($validated["logo"])) {
            $validated["logo"] = $request->file('logo')->store(
                'clients/logos', 'public'
            );
            if (File::exists(public_path('storage/' . $client->logo)) && $client->logo != "clients/logos/default.png") {
                File::delete(public_path('storage/' . $client->logo));
            }
        }
        $client->update($validated);

        return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            //TODO delete avatar file
            $client = Client::find($id);
            if ($client->delete()) {
                return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully.');
            } else {
                return redirect()->route('admin.clients.index')->with('error', 'Client not deleted.');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.clients.index')->with('error', 'This client cannot be deleted due to existing dependencies (project)');
        }
    }
}
