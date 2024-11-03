<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cart' => ['required','array','min:1'],
            'cart.*.id' => ['required','string'],
            'cart.*.quantity' => 'nullable|integer|min:1',

            'message' => ['sometimes','string','max:500'],
            'payment' => ['required','array'],
            'payment.code' => ['required','string','in:cod,gcash,other']
        ];
    }

    public function messages(): array
    {
        return [
            'cart.required' => 'The cart cannot be empty.',
            'cart.*.id.required' => 'Each product must have an ID.',
            'cart.*.price.required' => 'Each product must have a price.',
            'message.max' => 'Message should below 500 characters',
            'payment.required' => 'Payment information is required.',
            'payment.method.in' => 'The payment method is invalid',
            'payment.code.required' => 'The payment method is required',
        ];
    }
}
