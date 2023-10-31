<?php

namespace App\Http\Controllers\Api\Prospect;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Prospect\ProspectsStoreRequest;
use App\Http\Requests\Admin\Prospect\ProspectStoreRequest;
use App\Http\Requests\Admin\Prospect\ProspectUpdateRequest;
use App\Http\Resources\ProspectResource;
use App\Models\Prospect;
use Illuminate\Http\Request;
use PHPUnit\Exception;

class ProspectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $campaign_id = $request->input('campaign_id');
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);
            $query = $campaign_id ? Prospect::where('campaign_id', $campaign_id)->skip($offset)->take($limit)
                : Prospect::skip($offset)->take($limit);

            $prospect = $query->get();
            return response(ProspectResource::collection($prospect));
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
//        try {
//            $project_id = $request->input('project_id');
//            if($project_id) {
//                $projects = Project::find($project_id);
//                $mailboxes = $projects->mailboxes;
//                return response(["mailboxes" => $mailboxes]);
//            } else {
//                $mailboxes = Mailbox::select('id', 'email', 'name')->get();
//                $projects = Project::select('id', 'name')->get();
//                return response(["mailboxes" => $mailboxes, "projects" => $projects]);
//            }
//        } catch (Exception $error) {
//            return response($error, 400);
//        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProspectsStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $prospects = $validated['prospects'];
            Prospect::insertProspects($prospects);
            return response(['message' => 'Prospects were successfully created']);
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Prospect $campaign)
    {
        try {
            $currentProspect = new ProspectResource($campaign);
            return response()->json($currentProspect);
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prospect $campaign)
    {
//        try {
//            $mailboxes = Mailbox::select('id', 'email', 'name')->get();
//            $projects = Project::select('id', 'name')->get();
//            return response(["campaign" => $campaign, "mailboxes" => $mailboxes, "projects" => $projects]);
//        } catch (Exception $error) {
//            return response($error, 400);
//        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProspectUpdateRequest $request, Prospect $prospect)
    {
        try {
            $validated = $request->validated();
            $prospect->update($validated);
            return response($prospect);
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $prospect = Prospect::find($id);
            $prospect->delete();
            return response('Prospect deleted successfully');
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    public function csvUpload(Request $request)
    {
        try {
            $campaign_id = $request->input('campaign_id');
            if ($request->hasFile('csv_file')) {
                $file = $request->file('csv_file');

                if ($file->getClientOriginalExtension() === 'csv') {
                    $filePath = $file->getRealPath();
                    $handle = fopen($filePath, 'r');

                    if ($handle !== false) {
                        fgetcsv($handle);

                        while (($data = fgetcsv($handle)) !== false) {
                           $prospect = new Prospect([
                                    'first_name' => $data[0],
                                    'last_name' => $data[1],
                                    'email' => $data[2],
                                    'campaign_id' => $campaign_id
                                ]);
                                $prospect->save();
                        }

                        fclose($handle);
                    }

                    return response()->json(['message' => 'Prospects uploaded successfully']);
                } else {
                    return response()->json(['error' => 'Invalid file format. Please upload a CSV file.']);
                }
            } else {
                return response()->json(['error' => 'No file uploaded']);
            }
        } catch (Exception $error) {
            return response($error, 400);
        }
    }
}
