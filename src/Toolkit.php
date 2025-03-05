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

		foreach ($requiredKeys as $requiredKey => $requiredType) {

			if(is_array($data[$requiredKey])){
				if(!self::checkObjectForm($requiredType, $data[$requiredKey])) return false;
				continue;
			}

			
			if(!isset($data[$requiredKey])) return false;
			if(gettype($data[$requiredKey]) != $requiredType) return false;
		}
		return true;
	}


}