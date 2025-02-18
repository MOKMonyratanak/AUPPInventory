<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(){   
        return true;
    }

    public function rules()
    {
        $companyId = $this->route('company')->id; // Get the brand ID from the route

        return [
            'name' => 'required|string|max:255|unique:brands,name,' . $companyId,
            'description' => 'nullable|string|max:255',
        ];
    }
}
