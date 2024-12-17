<?php

namespace App\Http\Controllers;

use App\Models\DeviceType;
use Illuminate\Http\Request;

class DeviceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deviceTypes = DeviceType::all();
        return view('device_types.index', compact('deviceTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('device_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        DeviceType::create($request->all());

        return redirect()->route('device_types.index')->with('success', 'Device type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DeviceType $deviceType)
    {
        return view('device_types.show', compact('deviceType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeviceType $deviceType)
    {
        return view('device_types.edit', compact('deviceType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeviceType $deviceType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $deviceType->update($request->all());

        return redirect()->route('device_types.index')->with('success', 'Device type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeviceType $deviceType)
    {
        $deviceType->delete();

        return redirect()->route('device_types.index')->with('success', 'Device type deleted successfully.');
    }
}
