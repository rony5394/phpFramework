<?php
namespace Rony539\PhpFramework;

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

	private function setResponseCode(int $responseCode): int {
		http_response_code($responseCode);return $responseCode;
	}

	public function dispatch(string $requestedHttpMethod, string $requestedHttpPath): int {

		if(!isset($this->routes[$requestedHttpPath]))return $this->setResponseCode(404);
		if(!isset($this->routes[$requestedHttpPath][$requestedHttpMethod]))return $this->setResponseCode(405);

		ob_start();
			foreach ($this->routes[$requestedHttpPath][$requestedHttpMethod]["middlewares"] as $middlewareName) {
				$middlewareCallable = $this->middlewares[$middlewareName];

				$response_code = $middlewareCallable();
				if($response_code)return $this->setResponseCode($response_code);
			}


		$response_code = $this->routes[$requestedHttpPath][$requestedHttpMethod]["handler"]();

		if(!is_int($response_code)){
			throw new \Exception("Route $requestedHttpMethod '$requestedHttpPath' did not returned status code!");
		}
		return $this->setResponseCode($response_code);
	}



}