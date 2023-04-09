<?php

namespace App\Utils;

use CodeIgniter\HTTP\Message;

class SuccessResponse
{
    private $code;
    private $message;
    private $result;


	public function __construct($code, $message, $result) {
		$this->code = $code;
		$this->message = $message;
		$this->result = $result;
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
	public function getResult() {
		return $this->result;
	}

	/**
	 * @param mixed $code 
	 * @param mixed $message 
	 * @param mixed $result 
	 */

}