<?php
require_once __DIR__."/../vendor/autoload.php";

class RouterTest extends \Rony539\PhpFramework\TestSystem {
	private \Rony539\PhpFramework\Router $router;

	function setUp() {
		$this->router = new \Rony539\PhpFramework\Router;
	}

	static function mockHandlerer(){
		return 911;
	}

	function test404(){
		$httpStatusCode = $this->router->dispatch("GET", "/notExisting");
		$this->assertEquals($httpStatusCode, 404);
	}

	function test405(){
		$this->router->route("GET", "/getOnly", "RouterTest::mockHandlerer");
		$httpStatusCode = $this->router->dispatch("POST", "/getOnly");
		$this->assertEquals($httpStatusCode, 405);
	}

	function testDispatch(){
		$this->router->route("GET", "/itWorksUnderWater", "RouterTest::mockHandlerer");
		$httpStatusCode = $this->router->dispatch("GET", "/itWorksUnderWater");

		$this->assertEquals($httpStatusCode, 911);
	}

}

new RouterTest();