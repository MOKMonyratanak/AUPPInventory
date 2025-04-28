<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Asset;
use App\Models\Company;
use App\Models\ActivityLog;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\Emailpdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::with('company');
    
            // Restrict results if the logged-in user is a manager
            if (Auth::user()->role !== 'admin') {
                $query->where('company_id', Auth::user()->company_id);
            }
        
            // Validate the search input
            $validated = $request->validate([
                'search' => ['nullable', 'string', 'max:255', 
                    'not_regex:/<\s*script.*?>.*?<\s*\/\s*script\s*>/i'], // Disallow script tags
            ]);
        
            // Apply the search filter if input is valid
            if ($request->filled('search')) {
                $search = htmlspecialchars($validated['search']); // Sanitize input
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%")
                        ->orWhereHas('company', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('position', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            }
        
            // Get the result
            $users = $query->paginate(20);
            return view('users.index', compact('users'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function create()
    {
        try {
                    // Check if the logged-in user is a manager
        if (Auth::user()->role !== 'admin') {
            // Restrict to the manager's company only
            $companies = Company::where('id', Auth::user()->company_id)->get();
        } else {
            // Fetch all companies for admins
            $companies = Company::all();
        }
        $positions = Position::all();
        return view('users.create', compact('companies','positions'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function store(StoreUserRequest $request)
    {
        try {
            $user = Auth::user();
    
            $newUser = User::create([
                'id' => $request->id, // Explicitly assign 'id'
                ...$request->validated(), // Merge all validated fields
                'password' => $request->role !== 'user' ? Hash::make($request->password) : null, // Ensure hashed password
            ]);
        
            ActivityLog::create([
                'admin_id' => auth()->id(),
                'user_id' => $newUser->id, // Use newly created user's ID
                'action' => 'create user',
                'asset_tag' => null, // Not applicable for users
            ]);
        
            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
     

    public function show(Request $request, User $user)
    {
        try {
            // Restrict access if the logged-in user is a manager and the user does not belong to their company
            if (Auth::user()->role !== 'admin' && $user->company_id !== Auth::user()->company_id) {
                abort(403, 'Unauthorized action.');
            }
        
            return view('users.show', compact('user'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }    
    
    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
            $companies = Company::all();
            $positions = Position::all();
    
            // Restrict managers from editing admins or other managers
            if (auth()->user()->role !== 'admin' && ($user->role === 'admin' || $user->role === 'manager')) {
                abort(403, 'Unauthorized action.');
            }
    
            return view('users.edit', compact('user', 'companies','positions'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $userData = $request->validated();
    
            // Remove password from the validated data if not provided
            if (!$request->filled('password')) {
                unset($userData['password']);
            } else {
                // Hash the new password before updating
                $userData['password'] = Hash::make($request->password);
            }
        
            $user->update($userData);
        
            ActivityLog::create([
                'admin_id' => auth()->id(),
                'user_id' => $user->id, // Log the affected user
                'action' => 'update user',
                'asset_tag' => null, // Not applicable for users
            ]);
        
            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function destroy(User $user)
    {
        try {
                    // Check if the authenticated user is an admin
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('users.index')->with('error', 'Unauthorized access. Only admins can delete device users.');
        }
        // Check if the user has ever been issued an asset
        $wasIssued = ActivityLog::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('admin_id', $user->id);
        })
        ->where('action', 'issue')
        ->exists();

        if ($wasIssued) {
            return redirect()->route('users.index')->with('error', 'This user cannot be deleted as they have issued/been issued an asset before. You might want to mark them as resigned if they no longer work here.');
        }
        $userId = $user->id;
        $user->delete();

        ActivityLog::create([
            'admin_id' => auth()->id(),
            'user_id' => $userId, // Log the deleted user ID
            'action' => 'delete user',
            'asset_tag' => null, // Not applicable for users
        ]);
        
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function history(User $user)
    {
        try {
            // Fetch audit logs for the specific user
            $history = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

            return view('users.history', compact('user', 'history'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function assignAssets(Request $request, User $user)
    {
        try {
                    // Ensure the manager can only manage users in their own company
        if (Auth::user()->role !== 'admin' && $user->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
    
        // Validate the request
        $request->validate([
            'user_assets' => 'array', // Ensure it's an array
            'user_assets.*' => 'exists:assets,asset_tag', // Each asset must exist in the database
            'purpose' => 'required|string|in:event,daily_work', // Ensure purpose is valid
        ]);
    
        $purpose = $request->input('purpose');
        $selectedAssets = $request->input('user_assets', []);
    
        // Flatten the array to avoid nested array issues
        $selectedAssets = collect($selectedAssets)->flatten()->toArray();
    
        // Begin a transaction for safe updates
        DB::transaction(function () use ($selectedAssets, $user, $purpose) {
            // STEP 1: Fetch current assets for the user and purpose BEFORE unassignment
            $previousAssets = Asset::where('user_id', $user->id)
                ->where('purpose', $purpose)
                ->get();
    
            $previousAssetTags = $previousAssets->pluck('asset_tag')->toArray();
    
            // STEP 2: Identify assets to return (old assets not in the new selected list)
            $assetsToReturn = array_diff($previousAssetTags, $selectedAssets);
    
            // STEP 3: Unassign only the assets that are not selected again
            if (!empty($assetsToReturn)) {
                Asset::whereIn('asset_tag', $assetsToReturn)
                    ->where('user_id', $user->id)
                    ->where('purpose', $purpose)
                    ->update([
                        'user_id' => null,
                        'status' => 'available',
                        'checked_out_by' => null,
                        'purpose' => null,
                    ]);
    
                // Log return activity
                foreach ($assetsToReturn as $assetTag) {
                    ActivityLog::create([
                        'admin_id' => Auth::id(),
                        'user_id' => $user->id,
                        'action' => 'return',
                        'asset_tag' => $assetTag,
                    ]);
                }
            }
    
            // STEP 4: Identify new assets to assign (not already assigned to the user)
            $assetsToAssign = array_diff($selectedAssets, $previousAssetTags);
    
            // STEP 5: Assign new assets
            if (!empty($assetsToAssign)) {
                $newAssets = Asset::whereIn('asset_tag', $assetsToAssign)
                    ->where('status', 'available')
                    ->get();
    
                foreach ($newAssets as $asset) {
                    $asset->update([
                        'user_id' => $user->id,
                        'status' => 'issued',
                        'checked_out_by' => Auth::id(),
                        'purpose' => $purpose,
                    ]);
    
                    // Log issuance activity
                    ActivityLog::create([
                        'admin_id' => Auth::id(),
                        'user_id' => $user->id,
                        'action' => 'issue',
                        'asset_tag' => $asset->asset_tag,
                    ]);
                }
            }
        });
    
        return back()->with('success', 'Assets updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage()); 
        }
    }

    public function issueAssetPage(Request $request, User $user)
    {
        try {
            $purpose = $request->query('purpose'); // Retrieve the selected purpose from query parameters
    
            // Validate the purpose parameter
            if (!$purpose || !in_array($purpose, ['event', 'daily_work'])) {
                return redirect()->route('users.show', $user->id)
                    ->with('error', 'Invalid purpose selected.');
            }
        
            // Fetch available assets
            $availableAssetsQuery = Asset::where('user_id', null)
                ->where('status', 'available') // Ensure only available assets are fetched
                ->whereIn('condition', ['new', 'moderate']); // Filter by specific conditions
        
            // Restrict managers to their own company's assets
            if (Auth::user()->role !== 'admin') {
                $availableAssetsQuery->where('company_id', Auth::user()->company_id);
            }
        
            $availableAssets = $availableAssetsQuery->get();
        
            // Fetch the user's assigned assets filtered by the selected purpose
            $userAssetsQuery = Asset::where('user_id', $user->id)
                ->where('purpose', $purpose); // Filter by the selected purpose
        
            // Optionally restrict user assets by company if needed
            if (Auth::user()->role !== 'admin') {
                $userAssetsQuery->where('company_id', Auth::user()->company_id);
            }
        
            $userAssets = $userAssetsQuery->get();
        
            return view('users.issue_assets', compact('user', 'availableAssets', 'userAssets', 'purpose'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function returnAsset(Request $request, User $user)
    {
        try {
                    // Ensure the manager can only manage users in their own company
        if (Auth::user()->role !== 'admin' && $user->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
    
        // Validate the request
        $request->validate([
            'asset_tag' => 'required|exists:assets,asset_tag', // Ensure asset exists in the database
        ]);
    
        $assetTag = $request->input('asset_tag');
    
        // Begin a transaction for safe updates
        DB::transaction(function () use ($assetTag, $user) {
            $asset = Asset::where('asset_tag', $assetTag)
                ->where('user_id', $user->id)
                ->firstOrFail();
    
            // Record the current purpose before unassigning
            $purpose = $asset->purpose;
    
            // Unassign the asset
            $asset->update([
                'user_id' => null,
                'status' => 'available',
                'checked_out_by' => null,
                'purpose' => null,
            ]);
    
            // Log the return activity
            ActivityLog::create([
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'action' => 'return',
                'asset_tag' => $assetTag,
            ]);
        });
    
        return back()->with('success', 'Asset returned successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function print(User $user, Request $request)
    {
        try {
            $purpose = $request->input('purpose');
    
            // Restrict manager access to assets of users in their company
            if (Auth::user()->role !== 'admin' && $user->company_id !== Auth::user()->company_id) {
                abort(403, 'Unauthorized action.');
            }
        
            // Fetch only assets with the specified purpose
            $userAssets = $user->assets()
                // ->where('company_id', Auth::user()->company_id)
                ->where('purpose', $purpose) // Filter by purpose
                ->get();
        
            // Add authenticated user and date for agreement
            $loggedInUser = Auth::user();
            $todayDate = Carbon::now()->format('d-M-Y');
        
            // Determine which view to load based on the purpose
            $view = ($purpose === 'event') ? 'users.print-event-assets' : 'users.print-assets';
        
            // Pass necessary data to the view
            return view($view, compact('user', 'userAssets', 'loggedInUser', 'todayDate', 'purpose'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function checkIssuedAssets(User $user)
    {
        try {
                    // Check if the user has any issued assets
        $issuedAssets = Asset::where('user_id', $user->id)->where('status', 'issued')->count();

        // Return JSON response indicating if there are issued assets
        if ($issuedAssets > 0) {
            return response()->json([
                'hasIssuedAssets' => true,
                'message' => 'The user cannot resign until all issued assets are returned to the company.'
            ]);
        }

        return response()->json(['hasIssuedAssets' => false]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function resign(User $user)
    {
        try {
                    // Check if the user has any issued assets
        $issuedAssets = Asset::where('user_id', $user->id)->where('status', 'issued')->count();

        if ($issuedAssets > 0) {
            // Redirect with error message if there are issued assets
            return redirect()->route('users.index')->with([
                'error' => 'The user cannot resign until all issued assets are returned to the company.',
                'issuedAssets' => true // Flag to trigger the modal
            ]);
        }

        // Proceed with resignation if no issued assets
        if ($user->status === 'employed') {
            $user->update(['status' => 'resigned']);
            ActivityLog::create([
                'admin_id' => auth()->id(),
                'user_id' => $user->id, // Log the affected user
                'action' => 'resign user',
                'asset_tag' => null, // Not applicable for users
            ]);
            return redirect()->route('users.index')->with('success', 'User has been marked as resigned.');
        }

        // If user is already resigned, show an appropriate error message
        return redirect()->route('users.index')->with('error', 'The user is already resigned.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function emailPdf(User $user, Request $request)
    {
        try {
            $purpose = $request->input('purpose');
    
            // Restrict if manager tries to email assets of a user not in their company
            if (Auth::user()->role !== 'admin' && $user->company_id !== Auth::user()->company_id) {
                abort(403, 'Unauthorized action.');
            }
        
            // Fetch user assets filtered by purpose
            $userAssets = $user->assets()
                ->where('purpose', $purpose)
                ->get();
        
            // Add authenticated user and date
            $loggedInUser = Auth::user();
            $todayDate = now()->format('d-M-Y');
        
            // Generate PDF using the appropriate view
            $view = ($purpose === 'event') ? 'users.pdf-event-assets' : 'users.pdf-assets';
            $pdf = Pdf::loadView($view, compact('user', 'userAssets', 'loggedInUser', 'todayDate'));
        
            // Prepare email details
            $to = $user->email;
            $subject = "Asset Issuance Details";
            $msg = "Greetings, \n\n Kindly see the attached file for the information of the assets issued to you. \n\n Thank you, \n IT Support Office";
        
            // Send email with attachment
            Mail::to($to)->send(new Emailpdf($msg, $subject, $pdf->output(), $user));
            // return $pdf->download('assets.pdf');  //For testing
            return redirect()->back()->with('success', 'Email sent successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
