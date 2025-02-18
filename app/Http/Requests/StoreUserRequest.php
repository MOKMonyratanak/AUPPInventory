<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Modify if needed for authorization logic
    }

    public function rules()
    {
        $user = Auth::user();

        return [
            'id' => 'required|integer|max:1000000000|unique:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => [
                'required',
                'string',
                'in:admin,manager,user',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->role !== 'admin' && $value !== 'user') {
                        $fail('Managers can only create users with the role "user".');
                    }
                }
            ],
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->role !== 'admin' && $value != $user->company_id) {
                        $fail('You are only allowed to create users under your own company.');
                    }
                }
            ],
            'position_id' => 'required|integer|exists:positions,id',
            'contact_number' => [
                'required',
                'regex:/^0\d{8,9}$/'
            ],
            'status' => 'required|string|in:employed,resigned',
            'password' => $this->input('role') !== 'user' ? 'required|string|min:8|confirmed' : 'nullable',
        ];
    }
}
