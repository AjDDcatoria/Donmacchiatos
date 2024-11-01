<?php

namespace App\Services;

use App\Mail\Mailer;
use App\Models\Verification;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendOTP($email,$verification_code)
    {
        Mail::to($email)->send(new Mailer($verification_code));
    }
}