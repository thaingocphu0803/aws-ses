<?php

class HandleRequest
{	
	private $error = false;
	private $message = null;
	private $data = null;

	public function __construct(){}

	public function has_error(){
		return $this->error;
	}

	public function is_post(){
		if($this->has_error()){
			return $this;
		}

		if($_SERVER['REQUEST_METHOD'] !== 'POST'){
			$this->error  = true;
			$this->message = 'The request method is incorrected';
		}

		return $this;
	}

	public function is_contentType($type){
		if($this->has_error()){
			return $this;
		}

		$request_header = getallheaders();
		$lower_header = array_change_key_case($request_header);

		if(isset($lower_header['Content-Type']) && $lower_header['Content-Type'] !== $type){
			$this->error  = true;
			$this->message = 'The content type is unexpected';

		}
		return $this;
	}

	public function get_payload()
	{
		if($this->has_error()){
			return $this;
		}

		$payload = json_decode(file_get_contents('php://input'), true);

		if(empty($payload)){
			$this->error = true;
			$this->message = 'The request data is empty';

			return $this;
		}

		$this->data = $payload;

		return $this;

	}

	public function accept()
	{
		if($this->has_error()){
			return [
				'error_message' => $this->message,
				'data' => null
			];
		}

		return [
			'error_message' => null,
			'data' => $this->data
		];
	}
}