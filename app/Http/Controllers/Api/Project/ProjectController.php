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
            return response([
                "message" => "Problem with getting All Projects",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    public function getAllByUser(User $user)
    {
        try {
            $projects = $user->projects;
            return response(ProjectResource::collection($projects));
        } catch (Exception $error) {
            return response([
                "message" => "Problem with getting Projects of user",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $mailboxes = Mailbox::all();
            $users = User::all();
            $clients = Client::all();
            return response()->json([
                'mailboxes' => $mailboxes,
                'clients' => $clients,
                'users' => $users
            ]);
        } catch (Exception $error) {
            return response([
                "message" => "Problem with getting info for creating Project",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectStoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $validated['logo'] = isset($validated['logo'])
                ? $request->file('logo')->store('projects/logos', 'public')
                : 'projects/logos/default.png';

            $project = Project::create($validated);
            if ($validated['users'])
                $project->users()->attach($validated['users']);

            if ($validated['mailboxes'])
                $project->mailboxes()->attach($validated['mailboxes']);

            return response('Project created successfully');
        } catch (Exception $error) {
            return response([
                "message" => "Problem with store Project",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        try {
            $currentProject = new ProjectResource($project);
            return response($currentProject);
        } catch (Exception $error) {
            return response([
                "message" => "Problem with getting Project",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        try {
            $mailboxes = Mailbox::all();
            $users = User::all();
            $clients = Client::all();
            return response()->json([
                "mailboxes" => $mailboxes,
                'users' => $users,
                'clients' => $clients,
                'project' => $project
            ]);
        } catch (Exception $error) {
            return response([
                "message" => "Problem with editing Project",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectUpdateRequest $request, Project $project)
    {
        try {
            $validated = $request->validated();

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

            return response('Project updating successfully');
        } catch (Exception $error) {
            return response([
                "message" => "Problem with updating Project",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        try {
            //TODO delete avatar file
            $project->users()->detach();
            $project->mailboxes()->detach();
            $deletedProject = $project->name;
            $project->delete();

            return response($deletedProject);
        } catch (Exception $error) {
            return response([
                "message" => "Problem with destroying Project",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }
}
