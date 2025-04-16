<?php

namespace Rony539\PhpFramework;

class Toolkit{
	// I like this syntax :D
	static public string $requestedHttpMethod;
	static public string $requestedHttpRoute;

	static public object $requestJsonBodyParsed;

	
	static function init(): void{
		self::$requestedHttpRoute = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		self::$requestedHttpMethod = $_SERVER['REQUEST_METHOD'];

		$body = json_decode(file_get_contents("php://input"));

		self::$requestJsonBodyParsed = isset($body) ? $body : (object)[];
	}

	static function checkBodyForm(array | object $requiredKeys): bool{
		$body = [];
		if(!isset($_REQUEST))$body = $_REQUEST;
		if(!isset(self::$requestJsonBodyParsed))$body = self::$requestJsonBodyParsed;

		return self::checkObjectForm($requiredKeys, $body);
	}

	static function checkObjectForm(array | object $requiredKeys, array | object $data): bool{
		$requiredKeys = (array) $requiredKeys;
		$data = (array) $data;

		foreach ($requiredKeys as $requiredKey => $requiredType) {
			if(!isset($data[$requiredKey]))return false;

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