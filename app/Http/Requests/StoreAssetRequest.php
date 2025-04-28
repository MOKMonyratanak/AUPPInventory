<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Modify this if you want authorization logic
    }

    public function rules()
    {
        $user = auth()->user();

        return [
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
                    if ($user->role !== 'admin' && $value != $user->company_id) {
                        $fail('You can only create assets under your own company.');
                    }
                }
            ],
            'condition' => 'required|string|in:new,moderate,poor,defective,for repair,disposal,missing,stolen',
            'status' => 'required|string|in:available,issued',
            'user_id' => 'nullable|integer|exists:users,id',
            'checked_out_by' => 'nullable|integer|exists:users,id',
            'purpose' => 'nullable|string|in:daily_work,event',
            'note' => 'nullable|string|max:255'
        ];
    }
}
