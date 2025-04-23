<?php
require_once __DIR__."/../vendor/autoload.php";
use \Rony539\PhpFramework\Router;

class RouterTest extends \Rony539\PhpFramework\TestSystem {

	function setUp() {}

	static function mockHandlerer(){
		return 911;
	}

	function test404(){
		$httpStatusCode = Router::dispatch("GET", "/notExisting");
		$this->assertEquals($httpStatusCode, 404);
	}

	function test405(){
		Router::route("GET", "/getOnly", "RouterTest::mockHandlerer");
		$httpStatusCode = Router::dispatch("POST", "/getOnly");
		$this->assertEquals($httpStatusCode, 405);
	}

	function testDispatch(){
		Router::route("GET", "/itWorksUnderWater", "RouterTest::mockHandlerer");
		$httpStatusCode = Router::dispatch("GET", "/itWorksUnderWater");

		$this->assertEquals($httpStatusCode, 911);
	}

}

new RouterTest();