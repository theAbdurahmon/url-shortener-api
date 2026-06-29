<?php

namespace App\Exceptions;

use Exception;

class InvalidSlugException extends Exception
{
    public function errorMessage(): string {
        return "Invalid slug: {$this->getMessage()}. Please choose a different word.";
    }
}
