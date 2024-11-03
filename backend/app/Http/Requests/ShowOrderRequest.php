<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ShowOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool)$this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'view_scope' => ['required','in:customer,admin'],
            'status' => ['required','in:pending,canceled,declined,accepted,all']
        ];
    }

    public function messages(): array
    {
       return [
           'view_scope.required' => 'View scope is required!',
           'view_scope.in' => 'Invalid scoped!',
           'status.required' => 'Status is required!',
           'status.in' => 'Invalid status!'
       ];
    }
}
