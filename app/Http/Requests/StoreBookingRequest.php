<?php

namespace App\Http\Requests;

use App\Models\Resource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
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
            'resource_id' => [
                'required',
                'integer',
                Rule::exists('resources', 'id')->where(function ($query) {
                    $query->where('is_active', true)
                        ->whereNull('deleted_at');
                }),
            ],
            'user_id' => 'required|integer|exists:users,id',
            'start_time' => [
                'required',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    if (strtotime($value) > strtotime('+1 year')) {
                        $fail('Booking cannot be made more than 1 year in advance.');
                    }
                },
            ],
            'end_time' => [
                'required',
                'date',
                'after:start_time',
                function ($attribute, $value, $fail) {
                    if ($this->has('start_time')) {
                        $start = strtotime($this->start_time);
                        $end = strtotime($value);
                        $duration = ($end - $start) / 3600; // Duration in hours
                        
                        if ($duration > 24) {
                            $fail('Booking duration cannot exceed 24 hours.');
                        }
                    }
                },
            ],
            'notes' => 'nullable|string|max:500',
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
            'resource_id.required' => 'A resource must be selected.',
            'resource_id.exists' => 'The selected resource does not exist or is not available.',
            'user_id.required' => 'A user must be selected.',
            'user_id.exists' => 'The selected user does not exist.',
            'start_time.required' => 'The start time is required.',
            'start_time.after' => 'The booking must start in the future.',
            'end_time.required' => 'The end time is required.',
            'end_time.after' => 'The end time must be after the start time.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->has('resource_id') && 
                !$validator->errors()->has('start_time') && 
                !$validator->errors()->has('end_time')) {
                
                $resource = Resource::find($this->resource_id);
                if ($resource && !$resource->isAvailable($this->start_time, $this->end_time)) {
                    $validator->errors()->add(
                        'resource_id',
                        'The resource is not available for the selected time period.'
                    );
                }
            }
        });
    }
}
