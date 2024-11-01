<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function createUserWithEmail($email,$provider)
    {
        $new_user = User::create([
            'email' => $email,
            'provider' => $provider
        ]);

        return $new_user;
    }

    public function updateUser($data,$request): void
    {
        $user = $request->user();

        $user->firstname = $data['firstname'];
        $user->lastname = $data['lastname'];
        $user->contact_number = $data['contact_number'];
        $user->address = $data['address'];
        $user->is_setup = 1;
        $user->save();
    }
}
