<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 */
class OtpResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'verification_id' => is_array($this->resource) ? $this->resource['verification_id'] : $this->id,
            'message' => 'We send you a verfication code to your email.'
        ];
    }
}
