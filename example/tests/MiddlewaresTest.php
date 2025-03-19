<?php
require_once __DIR__."/../../vendor/autoload.php";

use \Rony539\PhpFramework\Example\Middlewares;
use \Rony539\PhpFramework\Toolkit;

class MiddlewaresTest extends \Rony539\PhpFramework\TestSystem{
	function setUp(){
		define("SECRET_PASSWORD", "12345");
	}

	function testStatelessAuth(){
		Toolkit::$requestJsonBodyParsed = (object)["password"=>"12345"];
		$output = Middlewares::statelessAuth();
		$this->assertEquals($output, null);

		Toolkit::$requestJsonBodyParsed = (object)[];
		$output = Middlewares::statelessAuth();
		$this->assertEquals($output, 400);

		Toolkit::$requestJsonBodyParsed = (object)["password"=>"not valid"];
		$output = Middlewares::statelessAuth();
		$this->assertEquals($output, 401);
	}
}

new MiddlewaresTest();