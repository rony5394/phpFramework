<?php

namespace Rony539\PhpFramework;
require_once __DIR__."/../vendor/autoload.php";

class Toolkit{
	// I like this syntax :D
	static public string $requestedHttpRoute;
	static public string $requestedHttpMethod;

	static public object $requestJsonBodyParsed;

	
	static function init(): void{
		self::$requestedHttpRoute = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		self::$requestedHttpMethod = $_SERVER['REQUEST_METHOD'];

		self::$requestJsonBodyParsed = json_decode(file_get_contents("php://input"));;
	}

	static function checkObjectForm(array | object $requiredKeys, array | object $data): bool{
		$requiredKeys = (array) $requiredKeys;
		$data = (array) $data;

		foreach ($requiredKeys as $key => $value) {
			if(!isset($data[$key])) return false;
			if(gettype($data[$key]) != $value) return false;
		}
		return true;
	}


}