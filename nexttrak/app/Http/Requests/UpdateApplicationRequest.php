<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Application;

class UpdateApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $application = Application::find($this->route('application'));
        return $application && $application->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_id' => 'sometimes|nullable|exists:companies,id',
            'company_name' => 'sometimes|required|string|max:255',
            'job_title' => 'sometimes|nullable|string|max:255',
            'salary' => 'sometimes|nullable|string|max:100',
            'status' => 'sometimes|required|string|in:Applied,Interviewing,Accepted,Rejected,Withdrawn',
            'application_date' => 'sometimes|required|date|before_or_equal:today',
            'follow_up_date' => 'sometimes|nullable|date|after_or_equal:application_date',
            'contact_email' => 'sometimes|nullable|email|max:255',
            'notes' => 'sometimes|nullable|string|max:1000',
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
            'status.in' => 'Status must be one of: Applied, Interviewing, Accepted, Rejected, Withdrawn.',
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
            'company_name' => $this->has('company_name') ? trim($this->company_name) : null,
            'job_title' => $this->has('job_title') ? ($this->job_title ? trim($this->job_title) : null) : null,
            'salary' => $this->has('salary') ? ($this->salary ? trim($this->salary) : null) : null,
            'contact_email' => $this->has('contact_email') ? ($this->contact_email ? trim($this->contact_email) : null) : null,
            'notes' => $this->has('notes') ? ($this->notes ? trim($this->notes) : null) : null,
        ]);
    }
} 