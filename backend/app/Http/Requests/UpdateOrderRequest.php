<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'order_id' => ['required','string'],
            'update' => ['required'],
            'update.payment' => ['sometimes','in:cod,cash'],
            'update.status' => ['sometimes','in:pending,declined,canceled,accepted'],
            'update.message' => ['sometimes','max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'update.payment.in' => "The selected payment is invalid!",
            'update.status.in' => "The selected status is invalid!",
            'update.message.max' => "The message must be below 500 characters!"
        ];
    }
}
