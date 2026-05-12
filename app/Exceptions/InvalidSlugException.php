<?php

namespace App\Exceptions;

use Exception;

class InvalidSlugException extends Exception
{
    public function errorMessage() {
        return "Invalid slug: {$this->getMessage()}. Please choose a different word.";
    }
}
