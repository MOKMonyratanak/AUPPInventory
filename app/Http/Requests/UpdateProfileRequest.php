<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Modify if needed for authorization logic
    }

    public function rules()
    {
        $userId = Auth::id();
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'position_id' => 'required|integer|exists:positions,id',
            'contact_number' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required_if:role,admin,manager,user',
        ];

        // Only allow admins to update `company_id`
        if ($user->role === 'admin') {
            $rules['company_id'] = 'required|integer|exists:companies,id';
        }

        return $rules;
    }
}
