<?php

namespace App\Http\Controllers\Api\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Project\ProjectStoreRequest;
use App\Http\Requests\Admin\Project\ProjectUpdateRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Client;
use App\Models\User;
use App\Models\Mailbox;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PHPUnit\Exception;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $query = Project::skip($offset)->take($limit);

            $projects = $query->get();

            return response(ProjectResource::collection($projects));
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    public function getAllByUser(User $user)
    {
        $user = User::where("user_id", "=", $user->id)->first();
        $projects = $user ? $user->projects : [];
        return response()->json(ProjectResource::collection($projects));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mailboxes = Mailbox::all();
        $linkedin_accounts = Linkedin::all();
        $users = User::all();
        $clients = Client::all();
        return view('admin.project.create',
            compact('mailboxes', 'linkedin_accounts', 'clients', 'periods', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectStoreRequest $request)
    {
        $validated = $request->validated();

        $validated['logo'] = isset($validated['logo'])
            ? $request->file('logo')->store('projects/logos', 'public')
            : 'projects/logos/default.png';

        $project = Project::create($validated);
        if ($validated['users'])
            $project->users()->attach($validated['users']);

        if ($validated['mailboxes'])
            $project->mailboxes()->attach($validated['mailboxes']);

        if ($validated['linkedin_accounts'])
            $project->linkedin_accounts()->attach($validated['linkedin_accounts']);

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        try {
            $currentProject = new ProjectResource($project);
            return response()->json($currentProject);
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = Project::findOrFail($id);

        $mailboxes = Mailbox::all();
        $linkedin_accounts = Linkedin::all();
        $users = User::all();
        $clients = Client::all();
        $periods = [
            [
                "title" => "Month"
            ],
            [
                "title" => "Quarter"
            ],
            [
                "title" => "Year"
            ],
        ];
        return view('admin.project.edit', compact('project', 'mailboxes', 'linkedin_accounts',
            'clients', 'periods', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectUpdateRequest $request, string $id)
    {
        $validated = $request->validated();

        $project = Project::findOrFail($id);

        if (isset($validated["logo"])) {
            $validated["logo"] = $request->file('logo')->store(
                'projects/logos', 'public'
            );
            if (File::exists(public_path('storage/' . $project->logo)) && $project->logo != "projects/logos/default.png") {
                File::delete(public_path('storage/' . $project->logo));
            }
        }
        $project->update($validated);

        $project->users()->sync($validated['users'] ?? []);

        $project->mailboxes()->sync($validated['mailboxes'] ?? []);

        $project->linkedin_accounts()->sync($validated['linkedin_accounts'] ?? []);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //TODO delete avatar file
        $project = Project::find($id);
        $project->users()->detach();
        $project->mailboxes()->detach();
        $project->linkedin_accounts()->detach();
        if ($project->delete()) {
            return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
        } else {
            return redirect()->route('admin.projects.index')->with('error', 'Project not deleted.');
        }
    }
}
