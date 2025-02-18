<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAssetRequest;
use App\Http\Requests\StoreAssetRequest;
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
        if (auth()->user()->role !== 'admin') {
            // Limit results to only the assets that belong to the manager's company
            $query->where('company_id', auth()->user()->company_id);
        }
        
        // Validate the search input
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255', 
                'not_regex:/<\s*script.*?>.*?<\s*\/\s*script\s*>/i'], // Disallow script tags
        ]);

        // Optional search functionality
        if ($request->filled('search')) {
            $search = htmlspecialchars($validated['search']); 
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
    
        // Paginate results (10 items per page)
        $assets = $query->paginate(20);
    
        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        $user = auth()->user();
        $companies = Company::all();

        // If the user is a manager, restrict to their own company
        if ($user->role !== 'admin') {
            $companies = Company::where('id', $user->company_id)->get();
        }

        $users = User::all();
        $deviceTypes = DeviceType::all();
        $brands = Brand::all();

        return view('assets.create', compact('users', 'companies', 'deviceTypes', 'brands'));
    }

    public function store(StoreAssetRequest $request)
    {
        $user = auth()->user();
    
        $asset = Asset::create($request->validated());
    
        ActivityLog::create([
            'admin_id' => $user->id,
            'user_id' => null,
            'action' => 'create asset',
            'asset_tag' => $request->asset_tag,
        ]);
    
        return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
    }

    public function show(Asset $asset)
    {
        // Check if the logged-in user is a manager and ensure they are only viewing assets from their company
        if (auth()->user()->role !== 'admin' && $asset->company_id !== auth()->user()->company_id) {
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
        if ($user->role !== 'admin' && $asset->company_id !== $user->company_id) {
            abort(403, 'You are not authorized to edit assets outside your company.');
        }

        $deviceTypes = DeviceType::all();
        $brands = Brand::all();
        $companies = Company::all();

        // If the user is a manager, restrict to their own company
        if ($user->role !== 'admin') {
            $companies = Company::where('id', $user->company_id)->get();
        }

        return view('assets.edit', compact('asset', 'deviceTypes', 'brands', 'companies'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $user = auth()->user();
    
        // Restrict managers to only update assets within their own company
        if ($user->role !== 'admin' && $asset->company_id !== $user->company_id) {
            return redirect()->route('assets.index')->withErrors('You are not authorized to edit this asset.');
        }
    
    // Update the asset with validated data
    $asset->update($request->validated());

        ActivityLog::create([
            'admin_id' => auth()->id(),
            'user_id' => null,
            'action' => 'update asset',
            'asset_tag' => $request->asset_tag,
        ]);
        
    
        return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        // Check if the authenticated user is an admin
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('assets.index')->with('error', 'Unauthorized access. Only admins can delete assets.');
        }

        // Check if the asset has ever been issued
        $wasIssued = ActivityLog::where('asset_tag', $asset->asset_tag)
        ->where('action', 'issue')
        ->exists();

        if ($wasIssued) {
            return redirect()->route('assets.index')->with('error', 'This asset cannot be deleted as it has been issued before. You might want to mark it defective instead if it\'s no longer usable.');
        }

        $assetTag = $asset->asset_tag;
        $asset->delete();

        ActivityLog::create([
            'admin_id' => auth()->id(),
            'user_id' => null, // Not applicable for assets
            'action' => 'delete asset',
            'asset_tag' => $assetTag, // Log the deleted asset's tag
        ]);

        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }

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
        $history = ActivityLog::with(['performedBy', 'affectedUser'])
            ->where('asset_tag', $assetTag)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    
        // Return the history view with the asset and its logs
        return view('assets.history', compact('asset', 'history'));
    }
}
