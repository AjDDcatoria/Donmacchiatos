<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
class EditProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user->role == 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required','string'],
            'image_url' => [
                'required',
                'max:2048',
                function ($attribute, $value, $fail) {
                    // Check if value is a string and validate as a URL
                    if (is_string($value)) {
                        if (!filter_var($value, FILTER_VALIDATE_URL)) {
                            $fail("The $attribute must be a valid URL.");
                        }
                    }
                    // Check if value is an uploaded file and validate as an image
                    elseif ($value instanceof UploadedFile) {
                        $rules = ['image', 'mimes:jpeg,png,jpg,gif'];
                        $validator = Validator::make([$attribute => $value], [$attribute => $rules]);
                        if ($validator->fails()) {
                            $fail("The $attribute must be a valid image file.");
                        }
                    }
                    // If neither a string nor an uploaded file, fail validation
                    else {
                        $fail("The $attribute must be either a valid URL or an image file.");
                    }
                }
            ],
            'name' => ['required','string'],
            'price' => ['required','numeric','min:1']
        ];
    }
}
