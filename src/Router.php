<?php
namespace Rony539\PhpFramework;
require_once __DIR__."/../vendor/autoload.php";

class Router {
	// I could make it private but there will no use for protected
	protected $routes = [];

	public function route(string $httpMethod, string $httpPath, callable $handler){
		$this->routes[$httpPath][$httpMethod] = $handler;
	}

	public function dispatch(string $requestedHttpMethod, string $requestedHttpPath): int | object {

		if(!isset($this->routes[$requestedHttpPath]))return 404;
		if(!isset($this->routes[$requestedHttpPath][$requestedHttpMethod]))return 405;

		$this->routes[$requestedHttpPath][$requestedHttpMethod]();
	}



}