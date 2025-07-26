<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // User is already authenticated via middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255|trim',
            'job_title' => 'nullable|string|max:255|trim',
            'salary' => 'nullable|string|max:100|trim',
            'status' => 'required|string|in:Applied,Interviewing,Offered,Rejected,Withdrawn|trim',
            'application_date' => 'required|date|before_or_equal:today',
            'follow_up_date' => 'nullable|date|after_or_equal:application_date',
            'contact_email' => 'nullable|email|max:255|trim',
            'notes' => 'nullable|string|max:1000|trim',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'company_name.max' => 'Company name cannot exceed 255 characters.',
            'status.in' => 'Status must be one of: Applied, Interviewing, Offered, Rejected, Withdrawn.',
            'application_date.before_or_equal' => 'Application date cannot be in the future.',
            'follow_up_date.after_or_equal' => 'Follow-up date must be on or after the application date.',
            'contact_email.email' => 'Please enter a valid email address.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_name' => trim($this->company_name),
            'job_title' => $this->job_title ? trim($this->job_title) : null,
            'salary' => $this->salary ? trim($this->salary) : null,
            'contact_email' => $this->contact_email ? trim($this->contact_email) : null,
            'notes' => $this->notes ? trim($this->notes) : null,
        ]);
    }
} 