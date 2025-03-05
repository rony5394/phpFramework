<?php

require_once __DIR__."/../vendor/autoload.php";

use \Rony539\PhpFramework\Toolkit;

class ToolkitTest extends \Rony539\PhpFramework\TestSystem{
	function setUp(){}

	function testCheckObjectForm(){
		$output = Toolkit::checkObjectForm(["idk"=>"boolean"],["idk"=>true]);
		$this->assertEquals($output, true);
		$output = Toolkit::checkObjectForm(
			[
				"isValid"=> "boolean",
				"user"=>[
					"name"=>"string",
					"password"=>"string",
					"money"=>"integer"
				]
			],
			[
				"isValid"=>true,
				"user"=>[
					"name"=>"rick",
					"password"=>"NeverGona",
					"money"=>15
				]
			]);
		$this->assertEquals($output, true);
	}
}
new ToolkitTest();