<?php

namespace App\Http\Controllers\Api\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ProjectStoreRequest;
use App\Http\Requests\Project\ProjectUpdateRequest;
use App\Http\Resources\Client\ClientCreateResource;
use App\Http\Resources\Mailbox\MailboxCreateResource;
use App\Http\Resources\Project\ProjectEditResource;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Project\ProjectStoreResource;
use App\Http\Resources\User\UserCreateResource;
use App\Models\Client;
use App\Models\User;
use App\Models\Mailbox;
use App\Models\Project;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class ProjectController extends Controller
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

            $query = Project::query()->skip($offset)->take($limit);

            $projects = $query->get();

            return response()->json(ProjectResource::collection($projects));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    public function getAllByUser(User $user): \Illuminate\Http\JsonResponse
    {
        try {
            $projects = $user->projects;
            return response()->json(ProjectResource::collection($projects));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
          try {
              $user = $request->user();
              $account_id = $user->account_id;

              $mailboxes = Mailbox::query()->where('account_id', $account_id)->get();
              $users = User::query()->where('account_id', $account_id)->get();
              $clients = Client::query()->where('account_id', $account_id)->get();

              return response()->json([
                  'mailboxes' => MailboxCreateResource::collection($mailboxes),
                  'users' => UserCreateResource::collection($users),
                  'clients' => ClientCreateResource::collection($clients)
              ]);
          } catch (Exception $error) {
              return $this->respondError($error->getMessage());
          }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $user = $request->user();
            $validated['account_id'] = $user->account_id;

            $project = Project::create($validated);

            if (isset($validated['users']) && $validated['users'])
                $project->users()->attach($validated['users']);

            if (isset($validated['mailboxes']) && $validated['mailboxes'])
                $project->mailboxes()->attach($validated['mailboxes']);

            DB::commit();
            return $this->respondCreated(new ProjectStoreResource($project));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->respondWithSuccess(new ProjectResource($project));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Project $project): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();
            $account_id = $user->account_id;

            $mailboxes = Mailbox::query()->where('account_id', $account_id)->get();
            $users = User::query()->where('account_id', $account_id)->get();
            $clients = Client::query()->where('account_id', $account_id)->get();

            return response()->json([
                'mailboxes' => MailboxCreateResource::collection($mailboxes),
                'users' => UserCreateResource::collection($users),
                'clients' => ClientCreateResource::collection($clients),
                'project' => new ProjectEditResource($project)
            ]);
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectUpdateRequest $request, Project $project): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $project->update($validated);

            $project->users()->sync($validated['users'] ?? []);

            $project->mailboxes()->sync($validated['mailboxes'] ?? []);

            DB::commit();
            return $this->respondWithSuccess(new ProjectStoreResource($project));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {
            $project->users()->detach();
            $project->mailboxes()->detach();
            $project->delete();

            DB::commit();
            return $this->respondOk($project->name);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->respondError($error->getMessage());
        }
    }
}
