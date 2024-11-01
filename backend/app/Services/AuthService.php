<?php

namespace App\Services;

use App\Constants\Providers;
use App\Models\User;

class AuthService
{
    protected $user_service;

    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    public function authenticate($email,$provider)
    {
        $user = User::where('email',$email)
                    ->where('provider',$provider)
                    ->first();

        if($user) {
            $token = $user->createToken('token')->accessToken;
            return $token;
        }

        switch($provider) {
            case Providers::EMAIL:
                $this->user_service->createUserWithEmail($email,$provider);
                return $this->authenticate($email, $provider);
//            case Providers::FACEBOOK:
//                $this->user_service->createUserWithEmail($email,$provider);
//                return $this->authenticate($email, $provider);
//            case Providers::GOOGLE:
//                $this->user_service->createUserWithEmail($email,$provider);
//                return $this->authenticate($email, $provider);
            default:
                throw new \Exception('Invalid provider',400);
        }
    }
}
