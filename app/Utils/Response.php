<?php

namespace App\Utils;

use CodeIgniter\HTTP\Message;

class Response {
    private $code;
    private $message;
    private $success;
    private $data;
    private $error;
	private $result;

	public function __construct(int $code, $message, $success, $data = null, $error = null){
        $this->code = $code;
        $this->message = $message;
        $this->success = $success;
        $this->data = $data;
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


	public function getResponse() {
		$response =  [
			'success' => $this->success,
			'message' => $this->message,
		];

		if (!empty($this->result)) {
			$response = array_merge($this->result, $response);
		}

		if (!is_null($this->data)) {
			$response['data'] = $this->data;
		}

		if (!is_null($this->error)) {
			$response['error'] = $this->error;
		}

		return $response;
	}



	/**
	 * @param mixed $result 
	 * @return self
	 */
	public function setResult($result): self {
		$this->result = $result;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}
	
	/**
	 * @param mixed $data 
	 * @return self
	 */
	public function setData($data): self {
		$this->data = $data;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function isSuccess() {
		return $this->success;
	}

	/**
	 * @return mixed
	 */
	public function getError() {
		return $this->error;
	}
	
	/**
	 * @param mixed $error 
	 * @return self
	 */
	public function setError($error): self {
		$this->error = $error;
		return $this;
	}
}