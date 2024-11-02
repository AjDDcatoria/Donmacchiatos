<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddProductRequest extends FormRequest
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
            'name' => ['required','string'],
            'image' => ['required','image','mimes:jpeg,png,jpg,gif','max:2048'],
            'price' => ['required','numeric','min:1']
        ];
    }

    public function createProduct() {
        $data = $this->validated();

        $path = $this->file('image')->store('images/products');

        $newProduct = Product::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'image' => $path
        ]);

        return $newProduct;
    }
}