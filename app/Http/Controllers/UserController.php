<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Asset;
use App\Models\Company;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\Emailpdf;
use Barryvdh\DomPDF\Facade\Pdf;



class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::with('company'); // Ensure eager loading

        // Restrict results if the logged-in user is a manager
        if (Auth::user()->role === 'manager') {
            $query->where('company_id', Auth::user()->company_id);
        }
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhereHas('company', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                  });
        }
    
        $users = $query->get(); // Get the result with eager-loaded 'company' relationship
    
        return view('users.index', compact('users'));
    }
    
    public function create()
    {
        // Check if the logged-in user is a manager
        if (Auth::user()->role === 'manager') {
            // Restrict to the manager's company only
            $companies = Company::where('id', Auth::user()->company_id)->get();
        } else {
            // Fetch all companies for admins
            $companies = Company::all();
        }
    
        return view('users.create', compact('companies'));
    }
    
    public function store(Request $request)
    {
        $user = Auth::user();
    
        // Validation rules
        $request->validate([
            'id' => 'required|integer|unique:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->role === 'manager' && $value !== 'user') {
                        $fail('Managers can only create users with the role "user".');
                    }
                }
            ],
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->role === 'manager' && $value != $user->company_id) {
                        $fail('You are only allowed to create users under your own company.');
                    }
                }
            ],
            'position' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'password' => $request->role !== 'user' ? 'required|string|min:8|confirmed' : 'nullable',
        ]);
    
        // Create the user
        User::create([
            'id' => $request->id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'company_id' => $request->company_id,
            'position' => $request->position,
            'contact_number' => $request->contact_number,
            'status' => $request->status,
            'password' => $request->role !== 'user' ? bcrypt($request->password) : null,
        ]);
    
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }    

    public function show(Request $request, User $user)
    {
        // Restrict access if the logged-in user is a manager and the user does not belong to their company
        if (Auth::user()->role === 'manager' && $user->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
    
        // Filter available assets based on search query
        $availableAssets = Asset::where('status', 'available')
            ->where('company_id', Auth::user()->company_id) // Limit to the manager's company
            ->when($request->filled('asset_search'), function ($query) use ($request) {
                $search = $request->input('asset_search');
                $query->where('asset_tag', 'like', "%{$search}%")
                    ->orWhereHas('deviceType', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            })
            ->with('deviceType') // Eager load deviceType to avoid N+1 problem
            ->get();
    
        $userAssets = $user->assets()->with('deviceType')->get(); // Eager load deviceType for user's assets
    
        return view('users.show', compact('user', 'availableAssets', 'userAssets'));
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $companies = Company::all();

        // Restrict managers from editing admins or other managers
        if (auth()->user()->role === 'manager' && ($user->role === 'admin' || $user->role === 'manager')) {
            abort(403, 'Unauthorized action.');
        }

        // Proceed to show the edit view
        return view('users.edit', compact('user', 'companies'));
    }

    public function update(Request $request, User $user)
    {
        $loggedInUser = Auth::user();
    
        // Validation rules
        $request->validate([
            'id' => 'required|integer|unique:users,id,' . $user->id,
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($loggedInUser) {
                    if ($loggedInUser->role === 'manager' && $value !== 'user') {
                        $fail('Managers can only update users with the role "user".');
                    }
                }
            ],
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
                function ($attribute, $value, $fail) use ($loggedInUser) {
                    if ($loggedInUser->role === 'manager' && $value != $loggedInUser->company_id) {
                        $fail('You are only allowed to update users under your own company.');
                    }
                }
            ],
            'position' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'password' => $request->role !== 'user' && $request->filled('password') ? 'required|string|min:8|confirmed' : 'nullable',
        ]);
    
        // Update the user data
        $userData = [
            'id' => $request->id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'company_id' => $request->company_id,
            'position' => $request->position,
            'contact_number' => $request->contact_number,
            'status' => $request->status,
        ];
    
        if ($request->role !== 'user' && $request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }
    
        $user->update($userData);
    
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
    

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
    
    public function assignAssets(Request $request, User $user)
    {
        // Ensure the manager can only manage users in their own company
        if (Auth::user()->role === 'manager' && $user->company_id !== Auth::user()->company_id) {
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
                        'checked_out_by' => Auth::id(),
                        'user_id' => $user->id,
                        'action' => 'return',
                        'asset_tag' => $assetTag,
                        'purpose' => $purpose,
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
                        'checked_out_by' => Auth::id(),
                        'user_id' => $user->id,
                        'action' => 'issue',
                        'asset_tag' => $asset->asset_tag,
                        'purpose' => $purpose,
                    ]);
                }
            }
        });
    
        return back()->with('success', 'Assets updated successfully.');
    }
    
    
    
    
    
    
    

    public function issueAssetPage(Request $request, User $user)
    {
        $purpose = $request->query('purpose'); // Retrieve the selected purpose from query parameters
    
        // Validate the purpose parameter
        if (!$purpose || !in_array($purpose, ['event', 'daily_work'])) {
            return redirect()->route('users.show', $user->id)
                ->with('error', 'Invalid purpose selected.');
        }
    
        // Fetch available assets
        $availableAssetsQuery = Asset::where('user_id', null)
            ->where('status', 'available'); // Ensure only available assets are fetched
    
        // Restrict managers to their own company's assets
        if (Auth::user()->role === 'manager') {
            $availableAssetsQuery->where('company_id', Auth::user()->company_id);
        }
    
        $availableAssets = $availableAssetsQuery->get();
    
        // Fetch the user's assigned assets filtered by the selected purpose
        $userAssetsQuery = Asset::where('user_id', $user->id)
            ->where('purpose', $purpose); // Filter by the selected purpose
    
        // Optionally restrict user assets by company if needed
        if (Auth::user()->role === 'manager') {
            $userAssetsQuery->where('company_id', Auth::user()->company_id);
        }
    
        $userAssets = $userAssetsQuery->get();
    
        return view('users.issue_assets', compact('user', 'availableAssets', 'userAssets', 'purpose'));
    }
    
    


    
    
    public function print(User $user, Request $request)
    {
        $purpose = $request->input('purpose');
    
        // Restrict manager access to assets of users in their company
        if (Auth::user()->role === 'manager' && $user->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
    
        // Fetch only assets with the specified purpose
        $userAssets = $user->assets()
            ->where('company_id', Auth::user()->company_id)
            ->where('purpose', $purpose) // Filter by purpose
            ->get();
    
        // Add authenticated user and date for agreement
        $loggedInUser = Auth::user();
        $todayDate = Carbon::now()->format('d-M-Y');
    
        // Determine which view to load based on the purpose
        $view = ($purpose === 'event') ? 'users.print-event-assets' : 'users.print-assets';
    
        // Pass necessary data to the view
        return view($view, compact('user', 'userAssets', 'loggedInUser', 'todayDate', 'purpose'));
    }
    



public function checkIssuedAssets(User $user)
    {
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
    }

    public function resign(User $user)
    {
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
            return redirect()->route('users.index')->with('success', 'User has been marked as resigned.');
        }

        // If user is already resigned, show an appropriate error message
        return redirect()->route('users.index')->with('error', 'The user is already resigned.');
    }

    public function emailPdf(User $user, Request $request)
    {
        $purpose = $request->input('purpose');
    
        // Restrict if manager tries to email assets of a user not in their company
        if (Auth::user()->role === 'manager' && $user->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
    
        // Fetch user assets filtered by purpose
        $userAssets = $user->assets()
            ->where('company_id', Auth::user()->company_id)
            ->where('purpose', $purpose)
            ->get();
    
        // Add authenticated user and date
        $loggedInUser = Auth::user();
        $todayDate = now()->format('d-M-Y');
    
        // Generate PDF using the appropriate view
        $view = ($purpose === 'event') ? 'users.print-event-assets' : 'users.print-assets';
        $pdf = Pdf::loadView($view, compact('user', 'userAssets', 'loggedInUser', 'todayDate'));
    
        // Prepare email details
        $to = $user->email;
        $subject = "Asset Issuance Details";
        $msg = "Greetings, \n\n Kindly see the attached file for the information of the assets issued to you. \n\n Thank you, \n IT Support Office";
    
        // Send email with attachment
        Mail::to($to)->send(new Emailpdf($msg, $subject, $pdf->output(), $user));
        // return $pdf->download('assets.pdf');
        return redirect()->back()->with('success', 'Email sent successfully!');
    }
    
}
