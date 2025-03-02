<?php
namespace Rony539\PhpFramework;
require_once __DIR__."/../vendor/autoload.php";

class Router {
	// I could make it private but there will no use for protected
	protected $routes = [];

	public function route(string $httpMethod, string $httpPath, callable $handler){
		$this->routes[$httpPath][$httpMethod] = $handler;
	}

	public function dispatch(string $requestedHttpMethod, string $requestedHttpPath): void {

		if(!isset($this->routes[$requestedHttpPath])){http_response_code( 404);exit;}
		if(!isset($this->routes[$requestedHttpPath][$requestedHttpMethod])){http_response_code(405);exit;}

		$this->routes[$requestedHttpPath][$requestedHttpMethod]();
	}



}