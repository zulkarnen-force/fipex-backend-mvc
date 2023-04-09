<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception 
{

    private $errors;
    public function __construct(array $errors, $message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

}