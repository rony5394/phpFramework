<?php
namespace Rony539\PhpFramework;
require_once __DIR__."/../vendor/autoload.php";

class Router {
	// I could make it private but there will no use for protected
	protected $routes = [];

	public function route(string $httpMethod, string $httpPath, callable $handler): void{
		$this->routes[$httpPath][$httpMethod] = $handler;
	}

	public function dispatch(string $requestedHttpMethod, string $requestedHttpPath): int {

		if(!isset($this->routes[$requestedHttpPath]))return 404;
		if(!isset($this->routes[$requestedHttpPath][$requestedHttpMethod]))return 405;

		ob_start();
		$response_code = $this->routes[$requestedHttpPath][$requestedHttpMethod]();

		if(!is_int($response_code)){
			throw new \Exception("Route $requestedHttpMethod '$requestedHttpPath' did not returned status code!");
		}
		return $response_code;
	}



}