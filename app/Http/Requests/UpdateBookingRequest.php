<?php

namespace App\Http\Requests;

use App\Models\Resource;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
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
            'start_time' => [
                'sometimes',
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
                'required_with:start_time',
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
            'start_time.after' => 'The booking must start in the future.',
            'end_time.after' => 'The end time must be after the start time.',
            'end_time.required_with' => 'The end time is required when updating the start time.',
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
            if ($this->hasTimeChanges()) {
                if (!$this->route('booking')->canBeCancelled()) {
                    $validator->errors()->add('booking', 'This booking cannot be updated.');
                    return;
                }

                if (!$validator->errors()->has('start_time') && !$validator->errors()->has('end_time')) {
                    $startTime = $this->start_time ?? $this->route('booking')->start_time;
                    $endTime = $this->end_time ?? $this->route('booking')->end_time;
                    
                    if (!$this->route('booking')->resource->isAvailable($startTime, $endTime, $this->route('booking')->id)) {
                        $validator->errors()->add(
                            'resource_id',
                            'The resource is not available for the selected time period.'
                        );
                    }
                }
            }
        });
    }

    /**
     * Check if the request includes time changes
     */
    protected function hasTimeChanges(): bool
    {
        return $this->has('start_time') || $this->has('end_time');
    }
}
