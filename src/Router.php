<?php
namespace Rony539\PhpFramework;
require_once __DIR__."/../vendor/autoload.php";

class Router {
	// I could make it private but...
	protected $routes = [];
	protected $middlewares = [];

	public function route(string $httpMethod, string $httpPath, callable $handler, array $middlewares = []): void{
		$this->routes[$httpPath][$httpMethod]["handler"] = $handler;
		$this->routes[$httpPath][$httpMethod]["middlewares"] = $middlewares;
	}

	public function middleware(string $name, callable $handler): void{
		$this->middlewares[$name] = $handler;
	}

	public function dispatch(string $requestedHttpMethod, string $requestedHttpPath): int {

		if(!isset($this->routes[$requestedHttpPath]))return 404;
		if(!isset($this->routes[$requestedHttpPath][$requestedHttpMethod]))return 405;

		ob_start();
			foreach ($this->routes[$requestedHttpPath][$requestedHttpMethod]["middlewares"] as $middlewareName) {
				$middlewareCallable = $this->middlewares[$middlewareName];

				$response_code = $middlewareCallable();
				if($response_code)return $response_code;
			}


		$response_code = $this->routes[$requestedHttpPath][$requestedHttpMethod]["handler"]();

		if(!is_int($response_code)){
			throw new \Exception("Route $requestedHttpMethod '$requestedHttpPath' did not returned status code!");
		}
		return $response_code;
	}



}