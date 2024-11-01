<?php

namespace App\Exceptions;

use Exception;

class OtpException extends Exception
{
    public static function RecordNotfound(): OtpException
    {
        return new self('Verification OTP not found!',404);
    }

    public static function Expired(): OtpException
    {
        return new self('The verification code is expired!',409);
    }

    public static function Invalid(): OtpException
    {
        return new self('Invalid verification code!',400);
    }
}
