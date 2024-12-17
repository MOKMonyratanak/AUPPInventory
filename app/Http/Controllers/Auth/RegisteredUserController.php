<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $companies = Company::all();
        $roles = ['admin', 'manager', 'employee']; // Predefined roles

        return view('auth.register', compact('companies', 'roles'));
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation rules for all fields including role
        $request->validate([
            'id' => ['required', 'integer', 'unique:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'position' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:employed,resigned'],
            'role' => ['required', 'in:admin,manager,employee'], // Role validation
        ]);

        // Create user
        $user = User::create([
            'id' => $request->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->company_id,
            'position' => $request->position,
            'contact_number' => $request->contact_number,
            'status' => $request->status,
            'role' => $request->role,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'))->with('success', 'User registered successfully!');
    }
}
