<?php

use PHPUnit\Framework\TestCase;
use Rony539\PhpFramework\Router;

class RouterTest extends TestCase
{
	protected function setUp(): void
	{
		$refClass = new \ReflectionClass(Router::class);

		$routesProp = $refClass->getProperty('routes');
		$routesProp->setAccessible(true);
		$routesProp->setValue(null, []);

		$middlewaresProp = $refClass->getProperty('middlewares');
		$middlewaresProp->setAccessible(true);
		$middlewaresProp->setValue(null, []);
	}

	public function testRouteAndDispatch() {
		Router::route("GET", "/test", function() {
			return 200;
		});

		$status = Router::dispatch("GET", "/test");
		$this->assertEquals(200, $status);
	}

	public function testNotFound() {
		Router::route("GET", "/test", function() {
			return 200;
		});
		$status = Router::dispatch("GET", "/thisRouteShouldntExists");
		$this->assertEquals(404, $status);

	}

	public function testMethodNotAllowed() {
		Router::route("GET", "/test", function() {
			return 200;
		});
		$status = Router::dispatch("POST", "/test");
		$this->assertEquals(405, $status);
	
	}

	public function testMiddlewareIntercept() {
		Router::middleware("TestMiddleware", function() {
			return 503;
		});

		Router::route("GET", "/test", function() {
			return 200;
		}, ["TestMiddleware"]);

		$status = Router::dispatch("GET", "/test");
		$this->assertEquals(503, $status);
	}

	public function testMiddlewareContinueToHandler(): void {
		Router::middleware("ContinueMw", function () { return; });
		Router::route("GET", "/test", function () { return 201; }, ["ContinueMw"]);
		$status = Router::dispatch("GET", "/test");
		$this->assertSame(201, $status);
	}

	public function testHandlerMustReturnIntStatusCode(): void {
		Router::route("GET", "/bad", function () { return "oops"; });
		$this->expectException(\UnexpectedValueException::class);
		Router::dispatch("GET", "/bad");
	}

	public function testNonExistingMiddleware() {
		Router::route("GET", "/test", function() {
			return 200;
		}, ["TestMiddleware"]);

		$status = Router::dispatch("GET", "/test");
		$this->assertEquals(500, $status);
	}

}

