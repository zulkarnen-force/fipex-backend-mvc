<?php

namespace App\Utils;

use CodeIgniter\HTTP\Message;

class ErrorResponse
{
    private $code;
    private $message;
    private $error;

	public function __construct(int $code, string $message, $error){
        $this->code = $code;
        $this->message = $message;
		$this->error = $error;
    }

	/**
	 * @return string
	 */
	public function getMessage(): string {
		return $this->message;
	}

	/**
	 * @return int
	 */
	public function getCode(): int {
		return $this->code;
	}


	/**
	 * @return mixed
	 */
	public function getError() {
		return $this->error;
	}

}