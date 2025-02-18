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
        $deviceTypes = DeviceType::all();
        return view('device_types.index', compact('deviceTypes'));
    }

    public function create()
    {
        return view('device_types.create');
    }

    public function store(StoreDeviceTypeRequest $request)
    {
        DeviceType::create($request->validated());
        return redirect()->route('device_types.index')->with('success', 'Device type created successfully.');
    }

    public function show(DeviceType $deviceType)
    {
        return view('device_types.show', compact('deviceType'));
    }

    public function edit(DeviceType $deviceType)
    {
        return view('device_types.edit', compact('deviceType'));
    }

    public function update(UpdateDeviceTypeRequest $request, DeviceType $deviceType)
    {
        $deviceType->update($request->validated());
        return redirect()->route('device_types.index')->with('success', 'Device type updated successfully.');
    }

    public function destroy(DeviceType $deviceType)
    {
        // Check if the authenticated user is an admin
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('device_types.index')->with('error', 'Unauthorized access. Only admins can delete device types.');
        }
    
        try {
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
