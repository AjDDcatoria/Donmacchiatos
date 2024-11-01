<?php

namespace App\Services;

use App\Exceptions\OtpException;
use App\Models\Verification;
use Carbon\Carbon;

class OtpService
{
    protected MailService $mail_service;

    public function __construct(MailService $mail_service)
    {
        $this->mail_service = $mail_service;
    }

    public function generate($email): Verification
    {
        $verification_code = $this->genererate_code();

        $new_verfication = Verification::create([
            'email' => $email,
            'code' => $verification_code,
            'expired_at' => Carbon::now()->addMinutes(10)
        ]);

//        $this->mail_service->sendOTP($email,$verification_code);
        return $new_verfication;
    }

    /**
     * @throws OtpException
     */
    public function resend($data): void
    {
        $verification_code = $this->update_verification($data);
//        $this->mail_service->sendOTP($data['email'],$verification_code);
    }

    /**
     * @throws OtpException
     */
    public function verify($data): string
    {
        $verification_otp = $this->get_verification($data);

        $expired_at = Carbon::parse($verification_otp['expired_at']);
        $today = Carbon::now();

        if($today->greaterThan($expired_at)) {
            throw OtpException::Expired();
        }

        if($verification_otp['code'] != $data['verification_code']) {
            throw OtpException::Invalid();
        }

        $verification_otp['verified'] = 1;
        $verification_otp->save();

        return $verification_otp['email'];
    }

    protected function genererate_code(): int
    {
        return rand(10000, 99999);
    }

    /**
     * @throws OtpException
     */
    protected function get_verification($data): ?Verification
    {
        $verification_otp = Verification::where('id',$data["verification_id"])
            ->where('verified',0)
            ->first();

        if (!$verification_otp) {
            throw OtpException::RecordNotfound();
        }

        return $verification_otp;
    }

    /**
     * @throws OtpException
     */
    protected function update_verification($data): int
    {
        $verification_otp = $this->get_verification($data);
        $verification_code = $this->genererate_code();

        $verification_otp['code'] = $verification_code;
        $verification_otp['expired_at'] = Carbon::now()->addMinutes(10);
        $verification_otp->save();

        return $verification_code;
    }

}
