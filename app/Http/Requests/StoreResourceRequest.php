<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('resources', 'name')->whereNull('deleted_at'),
            ],
            'type' => [
                'required',
                'string',
                'max:50',
                Rule::in(['room', 'equipment', 'vehicle', 'other']),
            ],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The resource name is required.',
            'name.unique' => 'A resource with this name already exists.',
            'name.max' => 'The resource name cannot exceed 255 characters.',
            'type.required' => 'The resource type is required.',
            'type.in' => 'The resource type must be one of: room, equipment, vehicle, or other.',
            'description.max' => 'The description cannot exceed 1000 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->is_active ?? true,
        ]);
    }
}
