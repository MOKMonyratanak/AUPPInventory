<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Check if the user has the 'user' role
        if (Auth::user()->role == 'user' or Auth::user()->status == 'resigned') {
            // Log out the user immediately
            Auth::logout();

            // Invalidate the session to prevent session fixation
            $request->session()->invalidate();

            // Regenerate the CSRF token
            $request->session()->regenerateToken();

            // Redirect with an error message
            return redirect()->route('login')
                ->withErrors(['email' => 'You are not allowed to log in.']);
        }

        // Regenerate the session to prevent session fixation attacks
        $request->session()->regenerate();

        // Redirect the user to their intended destination (or dashboard if no intended destination)
        return redirect()->intended(route('users.index', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
