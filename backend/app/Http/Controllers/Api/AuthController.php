<?php

namespace App\Http\Controllers\Api;

use App\Constants\Providers;
use App\Exceptions\OtpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Resources\OtpResource;
use App\Http\Resources\TokenResource;
use App\Services\AuthService;
use App\Services\OtpService;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\JsonResponse;
use Exception;

class AuthController extends Controller
{
    protected OtpService $otp_service;
    protected AuthService $auth_service;

    public function __construct(
        OtpService $otp_service,
        AuthService $auth_service
    )
    {
        $this->otp_service = $otp_service;
        $this->auth_service = $auth_service;
    }

    /**
     * @throws OtpException
     */
    public function create_otp(SendOtpRequest $request): JsonResponse
    {
        $isResend = $request->query('resend') === 'true';
        $data = $request->validated();

        if($isResend) {
            $this->otp_service->resend($data);
            return (new OtpResource($data))
                ->response()
                ->setStatusCode(201);
        }

        $new_otp = $this->otp_service
            ->generate($data[Providers::EMAIL]);

        return (new OtpResource($new_otp))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @throws OtpException
     * @throws Exception
     */
    public function verify_otp(VerifyOtpRequest $request): TokenResource
    {
        $data = $request->validated();
        $verified_email = $this->otp_service->verify($data);
        $token = $this->auth_service
                    ->authenticate($verified_email,Providers::EMAIL);

        return new TokenResource([
            'token' => $token,
            'message' => 'Email verified successfully!'
        ]);
    }

    public function redirect($provider): JsonResponse
    {
        $url = Socialite::driver($provider)
                        ->stateless()
                        ->redirect()
                        ->getTargetUrl();

        return response()->json(['redirect' => $url],307);
    }
}
