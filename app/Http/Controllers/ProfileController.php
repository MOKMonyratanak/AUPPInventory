<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Company;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        try {
            $user = Auth::user();
            return view('profile.show', compact('user'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

     public function edit()
    {
        try {
            $user = Auth::user();
            $companies = Company::all();
            $positions = Position::all();
            return view('profile.edit', compact('user', 'companies', 'positions'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();
        
            // Fill other fields except password
            $data = $request->validated();
            unset($data['password']); // Remove password to prevent it from being set to null
        
            $user->fill($data);
        
            // Only update role if the user is an admin
            if ($user->role === 'admin') {
                $user->role = $request->role;
            }
        
            // Only update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
        
            $user->save();
        
            return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
