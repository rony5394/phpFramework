<?php

namespace Rony539\PhpFramework;

use OpenSwoole\Http\Server as HttpServer;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

class Router {
	// I could make it private but...
	/** @var array<string, array<string, array{"handler": callable, "middlewares": string[]}>> */
	static protected array $routes = [];
	/** @var array<string, callable> */
	static protected $middlewares = [];

	/** 
	 * @param string[] $middlewares
	 * @param callable(): int $handler
	 */
	static public function route(string $httpMethod, string $httpPath, callable $handler, array $middlewares = []): void {
		self::$routes[$httpPath][$httpMethod]["handler"] = $handler;
		self::$routes[$httpPath][$httpMethod]["middlewares"] = $middlewares;
	}

	/**
	 * @param callable(): ?int $handler
	 */
	static public function middleware(string $name, callable $handler): void {
		self::$middlewares[$name] = $handler;
	}

	public function dispatch(Request $request, Response $response):void {
		$requestedUri = $request->server["request_uri"];
		$requestedMethod = $request->getMethod();

		if(!is_string($requestedUri) || !is_string($requestedMethod)){
			$response->status(500, "Internal Server Error");
			// TODO: Add log
			$response->end("Internal Server Error");
			return;
		}

		if(!isset(self::$routes[$requestedUri])){
			$response->status(404, "Not Found");
			$response->end("Not Found");
			return;
		}

		if(!isset(self::$routes[$requestedUri][$requestedMethod])){
			$response->status(405, "Method Not Allowed");
			$response->end("Method Not Allowed");
			return;
		}

		ob_start();

		$requestedMiddlewares = self::$routes[$requestedUri][$requestedMethod]["middlewares"];
		foreach($requestedMiddlewares as $requestedMiddlewareName){
			if(!isset(self::$middlewares[$requestedMiddlewareName])){
				$response->status(500, "Internal Server Error");
				// TODO: Add Log
				ob_end_clean();
				$response->end("Internal Server Error");
				return;
			}
			$middlewareRes = self::$middlewares[$requestedMiddlewareName]();
			if(!is_integer($middlewareRes) && !is_null($middlewareRes)){
				$response->status(500, "Internal Server Error");
				// TODO: Add log
				ob_end_clean();
				$response->end();
				return;
			}

			if($middlewareRes != null){
				$output = ob_get_clean();
				if($output !== false){
					$response->status($middlewareRes);
					$response->end($output);
					return;
				}
				$response->status(500, "Internal Server Error");
				// TODO: Add log
				$response->end();
				return;
			}
		};

		$responseCode = self::$routes[$requestedUri][$requestedMethod]["handler"]();

		if(gettype($responseCode) != "integer"){
			$response->status(500, "Internal Server Error");
			// TODO: Add log
			$response->end("Internal Server Error");
			ob_end_clean();
			return;
		}

		$response->status($responseCode);

		$output = ob_get_clean();
		if($output === false){
			$response->status(500, "Internal Server Error");;
			// TODO: Add log
			$response->end("Internal Server Error");
			ob_end_clean();
			return;
		}
		$response->end($output);
	}

	static public function server(string $ip, int $port){
		/* if(!extension_loaded("openswoole")) */
		/* 	throw new Error("Openswoole extension is not loaded and required for calling Router::server if you want to use normal http router call Router::dispatch."); */

		$server = new HttpServer($ip, $port);
		$server->on("Request", function (Request $request, Response $response)
		{
			new Router()->dispatch($request, $response);
		});

		$server->start();
	}
}
