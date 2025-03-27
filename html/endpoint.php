<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../backend/AwsSES.php';

class Router
{
	private static $instance = null;	
	public $path;

	public function __construct(){
		$this->setURL();
	}

	public static function getInstance(){
		if(self::$instance === null){
			self::$instance = new Router();
		}
		return self::$instance;
	}

	//check and return request URL
	private function setURL()
	{
		if (empty($_SERVER['REQUEST_URI'])) {
			die('ERROR: Cannot find URL');
		}
		$request_uri = trim($_SERVER['REQUEST_URI'], '/');

		$end_point = str_replace('endpoint.php/', '', $request_uri);

		$this->path =  isset($end_point) ? strtolower($end_point) : NULL;

	}

	public function runRoute()
	{
		$sesClient = new AwsSES();

		switch($this->path){
			case 'send-mail':
				$sesClient->sendSesEmail();
				break;
			default:
				break;
		}
	}
}


$router = Router::getInstance();
$router->runRoute();
