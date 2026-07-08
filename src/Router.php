<?php

namespace Rony539\PhpFramework;

use Error;
use Exception;
use OpenSwoole\Http\Server;

class Router {
	// I could make it private but...
	static protected $routes = [];
	static protected $middlewares = [];

	static public function route(string $httpMethod, string $httpPath, callable $handler, array $middlewares = []): void {
		self::$routes[$httpPath][$httpMethod]["handler"] = $handler;
		self::$routes[$httpPath][$httpMethod]["middlewares"] = $middlewares;
	}

	static public function middleware(string $name, callable $handler): void {
		self::$middlewares[$name] = $handler;
	}

	static private function setResponseCode(int $responseCode): int {
		http_response_code($responseCode);return $responseCode;
	}

	static public function dispatch(string $requestedHttpMethod, string $requestedHttpPath): string | int {

		if(!isset(self::$routes[$requestedHttpPath]))return self::setResponseCode(404);
		if(!isset(self::$routes[$requestedHttpPath][$requestedHttpMethod]))return self::setResponseCode(405);

		try{
			ob_start();
			foreach (self::$routes[$requestedHttpPath][$requestedHttpMethod]["middlewares"] as $middlewareName) {
				$middlewareCallable = &self::$middlewares[$middlewareName];

				$response_code = is_callable($middlewareCallable) ? $middlewareCallable(): 500;
				if($response_code && !is_int($response_code))
					throw new \UnexpectedValueException("Middleware $middlewareName did not return int|null!");
				if($response_code)
					return self::setResponseCode($response_code);
			}

			$response_code = self::$routes[$requestedHttpPath][$requestedHttpMethod]["handler"]();

			if(!is_int($response_code)){
				throw new \UnexpectedValueException("Route $requestedHttpMethod '$requestedHttpPath' did not returned a valid status code!");
			}

			return ob_get_clean();
		}
		catch (Exception){
			ob_end_clean();
			return 500;
		}
	}

	static public function server(string $ip, int $port){
		if(!extension_loaded("openswoole"))
			throw new Error("Openswoole extension is not loaded and required for calling Router::server if you want to use normal http router call Router::dispatch.");

		$server = new \OpenSwoole\HTTP\Server($ip, $port);
		$server->on("Request", function(\OpenSwoole\Http\Request $request, \OpenSwoole\Http\Response $response)
		{
			$response->end(self::dispatch($request->getMethod(), $request->server["request_uri"]));
		});

		$server->start();
	}
}
