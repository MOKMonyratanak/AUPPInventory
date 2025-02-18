<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::all();
        return view('positions.index', compact('positions'));
    }

    public function create()
    {
        return view('positions.create');
    }

    public function store(StorePositionRequest $request)
    {

        Position::create($request->validated());
        return redirect()->route('positions.index')->with('success', 'Position created successfully.');
    }

    public function show(Position $position)
    {
        return view('positions.show', compact('position'));
    }

    public function edit(Position $position)
    {
        return view('positions.edit', compact('position'));
    }

    public function update(UpdatePositionRequest $request, Position $position)
    {
        $position->update($request->validated());
        return redirect()->route('positions.index')->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position)
    {
        // Check if the authenticated user is an admin
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('positions.index')->with('error', 'Unauthorized access. Only admins can delete positions.');
        }
    
        try {
            $position->delete();
            return redirect()->route('positions.index')->with('success', 'Position deleted successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Foreign key constraint violation
                return redirect()->route('positions.index')->with('error', 'Cannot delete this position because it is linked to existing users.');
            }
    
            return redirect()->route('positions.index')->with('error', 'An error occurred while deleting the position.');
        }
    } 
}
