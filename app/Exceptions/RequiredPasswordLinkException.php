<?php

namespace App\Exceptions;

use Exception;

class RequiredPasswordLinkException extends Exception
{
    public function errorMessage(): string {
         return "Password is required";
    }
}
