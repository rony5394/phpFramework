<?php
namespace Rony539\PhpFramework;

class Router {
	// I could make it private but...
	static protected $routes = [];
	static protected $middlewares = [];

	static public function route(string $httpMethod, string $httpPath, callable $handler, array $middlewares = []): void{
		self::$routes[$httpPath][$httpMethod]["handler"] = $handler;
		self::$routes[$httpPath][$httpMethod]["middlewares"] = $middlewares;
	}

	static public function middleware(string $name, callable $handler): void{
		self::$middlewares[$name] = $handler;
	}

	static private function setResponseCode(int $responseCode): int {
		http_response_code($responseCode);return $responseCode;
	}

	static public function dispatch(string $requestedHttpMethod, string $requestedHttpPath): int {

		if(!isset(self::$routes[$requestedHttpPath]))return self::setResponseCode(404);
		if(!isset(self::$routes[$requestedHttpPath][$requestedHttpMethod]))return self::setResponseCode(405);

		ob_start();
			foreach (self::$routes[$requestedHttpPath][$requestedHttpMethod]["middlewares"] as $middlewareName) {
				$middlewareCallable = self::$middlewares[$middlewareName];

				$response_code = $middlewareCallable();
				if($response_code)return self::setResponseCode($response_code);
			}


		$response_code = self::$routes[$requestedHttpPath][$requestedHttpMethod]["handler"]();

		if(!is_int($response_code)){
			throw new \Exception("Route $requestedHttpMethod '$requestedHttpPath' did not returned status code!");
		}
		return self::setResponseCode($response_code);
	}



}