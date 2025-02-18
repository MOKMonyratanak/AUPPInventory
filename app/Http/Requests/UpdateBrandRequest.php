<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Modify if you need specific authorization logic
    }

    public function rules()
    {
        $brandId = $this->route('brand')->id; // Get the brand ID from the route

        return [
            'name' => 'required|string|max:255|unique:brands,name,' . $brandId,
            'description' => 'nullable|string|max:255',
        ];
    }
}
