<?php

namespace App\Http\Controllers\Admin\Position;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Position\PositionStoreRequest;
use App\Http\Requests\Admin\Position\PositionUpdateRequest;
use App\Models\Position;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::all();
        return view('admin.position.index', compact('positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.position.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PositionStoreRequest $request)
    {
        $validated = $request->validated();
        Position::create($validated);
        return redirect()->route('admin.positions.index')->with('success', 'Position created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $position = Position::findOrFail($id);
        return view('admin.position.show', compact('position'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $position = Position::findOrFail($id);
        return view('admin.position.edit', compact('position'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PositionUpdateRequest $request, string $id)
    {
        $validated = $request->validated();
        $position = Position::findOrFail($id);
        $position->update($validated);
        return redirect()->route('admin.positions.index')->with('success', 'Position updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $position = Position::find($id);
            if($position->delete()) {
                return redirect()->route('admin.positions.index')->with('success', 'Position deleted successfully.');
            } else {
                return redirect()->route('admin.positions.index')->with('error', 'Position not deleted.');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.positions.index')->with('error', 'This position cannot be deleted due to existing dependencies (employee)');
        }
    }
}
