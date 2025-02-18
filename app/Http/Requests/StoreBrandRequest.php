<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Change if needed for authorization logic
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string|max:255',
        ];
    }
}
