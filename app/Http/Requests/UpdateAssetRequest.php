<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Modify if needed for specific authorization logic
    }

    public function rules()
    {
        $assetTag = $this->route('asset')->asset_tag; // Get the asset_tag from the route
        $user = auth()->user();

        return [
            'asset_tag' => 'required|string|max:255|unique:assets,asset_tag,' . $assetTag . ',asset_tag',
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
                        $fail('You can only assign assets to your own company.');
                    }
                }
            ],
            'condition' => 'required|string|in:new,moderate,poor,defective,for repair',
            'status' => 'required|string|in:available,issued',
            'user_id' => 'nullable|integer|exists:users,id',
            'checked_out_by' => 'nullable|integer|exists:users,id',
            'purpose' => 'nullable|string|in:daily_work,event',
            'note' => 'nullable|string|max:255',
        ];
    }
}
