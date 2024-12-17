<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\Company;
use App\Models\DeviceType;
use App\Models\Brand;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AssetController extends Controller
{

    public function index(Request $request)
    {
        $query = Asset::with(['user', 'checkedOutBy', 'company', 'deviceType', 'brand']);
        
        // Check if the logged-in user is a 'manager'
        if (auth()->user()->role === 'manager') {
            // Limit results to only the assets that belong to the manager's company
            $query->where('company_id', auth()->user()->company_id);
        }
    
        // Optional search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_no', 'like', "%{$search}%")
                  ->orWhere('condition', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%")
                  ->orWhereHas('deviceType', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('brand', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('checkedOutBy', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('company', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
    
        $assets = $query->get();
    
        return view('assets.index', compact('assets'));
    }
    

// Show form for creating a new asset
// AssetController.php

public function create()
{
    $user = auth()->user();
    $companies = Company::all(); // Default: for non-managers

    // If the user is a manager, restrict to their own company
    if ($user->role === 'manager') {
        $companies = Company::where('id', $user->company_id)->get();
    }

    $users = User::all();  // Fetch all users
    $deviceTypes = DeviceType::all(); // Fetch all device types
    $brands = Brand::all(); // Fetch all brands

    return view('assets.create', compact('users', 'companies', 'deviceTypes', 'brands'));
}


// Store the new asset in the database
// AssetController.php

public function store(Request $request)
{
    $user = auth()->user();

    // Additional validation: if manager, restrict to their own company
    $request->validate([
        'asset_tag' => 'required|string|max:255|unique:assets,asset_tag',
        'device_type_id' => 'required|integer|exists:device_types,id',
        'brand_id' => 'nullable|integer|exists:brands,id',
        'model' => 'nullable|string|max:255',
        'serial_no' => 'nullable|string|max:255',
        'company_id' => [
            'required',
            'integer',
            'exists:companies,id',
            function ($attribute, $value, $fail) use ($user) {
                if ($user->role === 'manager' && $value != $user->company_id) {
                    $fail('You can only create assets under your own company.');
                }
            }
        ],
        'condition' => 'required|string|max:255',
        'status' => 'required|string|in:available,issued',
        'user_id' => 'nullable|integer|exists:users,id',
        'checked_out_by' => 'nullable|integer|exists:users,id',
        'purpose' => 'nullable|string|max:255',
        'note' => 'nullable|string|max:500',
    ]);

    // Proceed with asset creation
    Asset::create([
        'asset_tag' => $request->asset_tag,
        'device_type_id' => $request->device_type_id,
        'brand_id' => $request->brand_id,
        'model' => $request->model,
        'serial_no' => $request->serial_no,
        'company_id' => $request->company_id,
        'condition' => $request->condition,
        'status' => $request->status,
        'user_id' => $request->user_id,
        'checked_out_by' => $request->checked_out_by,
        'purpose' => $request->purpose,
        'note' => $request->note,
    ]);

    return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
}


    

    public function show(Asset $asset)
    {
        // Check if the logged-in user is a manager and ensure they are only viewing assets from their company
        if (auth()->user()->role === 'manager' && $asset->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
    
        $asset->load(['user', 'checkedOutBy', 'company', 'deviceType', 'brand']);
        return view('assets.show', compact('asset'));
    }

    public function edit($id)
    {
        $user = auth()->user();
        $asset = Asset::findOrFail($id);

        // Restrict managers to only edit assets within their own company
        if ($user->role === 'manager' && $asset->company_id !== $user->company_id) {
            abort(403, 'You are not authorized to edit assets outside your company.');
        }

        $deviceTypes = DeviceType::all(); // Fetch all device types
        $brands = Brand::all(); // Fetch all brands
        $companies = Company::all(); // Default: for non-managers

        // If the user is a manager, restrict to their own company
        if ($user->role === 'manager') {
            $companies = Company::where('id', $user->company_id)->get();
        }

        return view('assets.edit', compact('asset', 'deviceTypes', 'brands', 'companies'));
    }

    public function update(Request $request, Asset $asset)
    {
        $user = auth()->user();
    
        // Restrict managers to only update assets within their own company
        if ($user->role === 'manager' && $asset->company_id !== $user->company_id) {
            return redirect()->route('assets.index')->withErrors('You are not authorized to edit this asset.');
        }
    
        // Validate the request data
        $request->validate([
            'asset_tag' => 'required|string|max:255|unique:assets,asset_tag,' . $asset->getKey() . ',asset_tag', // Unique validation, excluding the current asset
            'device_type_id' => 'required|integer|exists:device_types,id',     // Ensure device_type_id exists in device_types table
            'brand_id' => 'nullable|integer|exists:brands,id',                // Ensure brand_id exists in brands table
            'model' => 'nullable|string|max:255',                             // Optional model field
            'serial_no' => 'nullable|string|max:255',                         // Optional serial number field
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
                function ($attribute, $value, $fail) use ($user) {
                    // Managers can only assign assets to their own company
                    if ($user->role === 'manager' && $value != $user->company_id) {
                        $fail('You can only assign assets to your own company.');
                    }
                }
            ],
            'condition' => 'required|string|max:255',                         // Required condition field
            'status' => 'required|string|in:available,issued',                // Status must be either 'available' or 'issued'
            'user_id' => 'nullable|integer|exists:users,id',                  // Optional user_id, ensure it exists in users table
            'checked_out_by' => 'nullable|integer|exists:users,id',           // Optional checked_out_by field, ensure it exists in users table
            'purpose' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:500',
        ]);
    
        // Update the asset with the validated data
        $asset->update([
            'asset_tag' => $request->asset_tag,
            'device_type_id' => $request->device_type_id,
            'brand_id' => $request->brand_id,  // Update the brand_id
            'model' => $request->model,
            'serial_no' => $request->serial_no,
            'company_id' => $request->company_id,
            'condition' => $request->condition,
            'status' => $request->status,
            'user_id' => $request->user_id,
            'checked_out_by' => $request->checked_out_by,
            'purpose' => $request->purpose,
            'note' => $request->note,
        ]);
    
        return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
    }
    

    

    // Delete an asset from the database
    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }

    // Clone an existing asset
    public function clone($assetTag)
    {
        // Find the asset to clone
        $asset = Asset::where('asset_tag', $assetTag)->firstOrFail();

        // Pre-fill the data for the clone, excluding specific fields
        $cloneData = $asset->replicate(['asset_tag', 'user_id', 'checked_out_by', 'status', 'note'])->toArray();

        // Redirect to the create form with the pre-filled data
        $users = User::all();
        $companies = Company::all();
        $deviceTypes = DeviceType::all();
        $brands = Brand::all();
        return view('assets.create', ['asset' => $cloneData, 'users' => $users, 'companies' => $companies, 'deviceTypes' => $deviceTypes, 'brands' => $brands]);
    }

    public function history($assetTag)
    {
        // Fetch the asset using the provided asset_tag
        $asset = Asset::where('asset_tag', $assetTag)->firstOrFail();
    
        // Retrieve activity logs based on the asset_tag, eager-loading related users
        $activityLogs = ActivityLog::with(['performedBy', 'affectedUser'])
            ->where('asset_tag', $assetTag)
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Return the history view with the asset and its logs
        return view('assets.history', compact('asset', 'activityLogs'));
    }
    
    
}
