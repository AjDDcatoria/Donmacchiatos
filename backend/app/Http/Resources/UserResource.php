<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $current_user = [
            'id' => $this->id,
            'email' => $this->email,
            'role' => $this->role,
            'is_setup' => $this->is_setup,
            'provider' => $this->provider,
        ];

        if($this->is_setup) {
            $current_user['details'] = [
                'fullname' => $this->firstname . ' ' . $this->lastname,
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
                'address' => $this->address,
                'contact_number' => $this->contact_number,
                'avatar' => $this->profile_picture,
            ];
        }

        return $current_user;
    }
}