<?php

namespace Rony539\PhpFramework;

require_once __DIR__ . "/../vendor/autoload.php";

use OpenSwoole\Http\Server as HttpServer;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

class Router {
	// I could make it private but...
	/** @var array<string, array<string, array{"handler": callable, "middlewares": array<string, callable>}>> */
	static protected array $routes = [];
	/** @var array<string, callable> */
	static protected $middlewares = [];
	protected int $statusCode;

	/** 
	 * @param array<string, callable> $middlewares
	 */
	static public function route(string $httpMethod, string $httpPath, callable $handler, array $middlewares = []): void {
		self::$routes[$httpPath][$httpMethod]["handler"] = $handler;
		self::$routes[$httpPath][$httpMethod]["middlewares"] = $middlewares;
	}

	static public function middleware(string $name, callable $handler): void {
		self::$middlewares[$name] = $handler;
	}

	public function dispatch(Request $request, Response $response):void{
		$requestedUri = $request->server["request_uri"];
		$requestedMethod = $request->getMethod();

		if(gettype($requestedUri) != "string" || gettype($requestedMethod) != "string"){
			$response->status(500, "Internal Server Error");
			// TODO: Add log
			$response->end("Internal Server Error");
			return;
		}


		if(!array_key_exists($requestedUri, self::$routes)){
			$response->status(404, "Not Found");
			$response->end("Not Found");
			return;
		}

		if(!array_key_exists($requestedMethod, self::$routes[$requestedUri])){
			$response->status(405, "Method Not Allowed");
			$response->end("Method Not Allowed");
			return;
		}

		ob_start();

		$middlewares = self::$routes[$requestedUri][$requestedMethod]["middlewares"];
		foreach($middlewares as $middleware){
			$middlewareRes = $middleware();
			if(gettype($middlewareRes) !== "integer" && gettype($middlewareRes) !== "NULL"){
				$response->status(500, "Internal Server Error");
				// TODO: Add log
				$response->end();
				return;
			}

			if($middlewareRes != null){
				$response->status($middlewareRes);
				$response->end();
			}
		}

		$responseCode = self::$routes[$requestedUri][$requestedMethod]["handler"]();

		if(gettype($responseCode) != "integer"){
			$response->status(500, "Internal Server Error");
			//TODO: Add log
			$response->end("Internal Server Error");
			return;
		}

		$response->status($responseCode);

		$response->end(ob_get_clean());
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
