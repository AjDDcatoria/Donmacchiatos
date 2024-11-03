<?php

namespace App\Exceptions;

use Exception;

class RoleExceptions extends Exception
{
    public static function unAuthorized(): RoleExceptions
    {
        return new self('This action is unauthorized!',401);
    }
}
