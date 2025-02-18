<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Modify if needed for authorization logic
    }

    public function rules()
    {
        $user = $this->route('user'); // Get the user being updated
        $loggedInUser = Auth::user();

        return [
            'id' => 'required|integer|max:1000000000|unique:users,id,' . $user->id,
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => [
                'required',
                'string',
                'in:admin,manager,user',
                function ($attribute, $value, $fail) use ($loggedInUser) {
                    if ($loggedInUser->role !== 'admin' && $value !== 'user') {
                        $fail('Managers can only update users with the role "user".');
                    }
                }
            ],
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
                function ($attribute, $value, $fail) use ($loggedInUser) {
                    if ($loggedInUser->role !== 'admin' && $value != $loggedInUser->company_id) {
                        $fail('You are only allowed to update users under your own company.');
                    }
                }
            ],
            'position_id' => 'required|integer|exists:positions,id',
            'contact_number' => [
                'required',
                'regex:/^0\d{8,9}$/'
            ],
            'status' => 'required|string|in:employed,resigned',
            'password' => 'nullable|string|min:8|confirmed', // Allow nullable password
        ];
    }
}
