<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $deviceTypeId = $this->route('device_type')->id;
        return [
            'name' => 'required|string|max:255|unique:device_types,name,' . $deviceTypeId,
            'description' => 'nullable|string|max:255',
        ];
    }
}
