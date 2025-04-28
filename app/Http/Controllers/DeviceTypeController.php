<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceTypeRequest;
use App\Http\Requests\UpdateDeviceTypeRequest;
use App\Models\DeviceType;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class DeviceTypeController extends Controller
{
    public function index()
    {
        try {
            $deviceTypes = DeviceType::all();
            return view('device_types.index', compact('deviceTypes'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('device_types.create');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function store(StoreDeviceTypeRequest $request)
    {
        try {
            DeviceType::create($request->validated());
            return redirect()->route('device_types.index')->with('success', 'Device type created successfully.');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(DeviceType $deviceType)
    {
        try {
            return view('device_types.show', compact('deviceType'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit(DeviceType $deviceType)
    {
        try {
            return view('device_types.edit', compact('deviceType'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(UpdateDeviceTypeRequest $request, DeviceType $deviceType)
    {
        try {
            $deviceType->update($request->validated());
            return redirect()->route('device_types.index')->with('success', 'Device type updated successfully.');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(DeviceType $deviceType)
    {    
        try {
            // Check if the authenticated user is an admin
            if (auth()->user()->role !== 'admin') {
                return redirect()->route('device_types.index')->with('error', 'Unauthorized access. Only admins can delete device types.');
            }
            
            $deviceType->delete();
            return redirect()->route('device_types.index')->with('success', 'Device type deleted successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Foreign key constraint violation
                return redirect()->route('device_types.index')->with('error', 'Cannot delete this device type because it is linked to existing assets.');
            }
    
            return redirect()->route('device_types.index')->with('error', 'An error occurred while deleting the device type.');
        }
    }
}
