<?php
namespace Rony539\PhpFramework\Example;
require_once __DIR__."/../../vendor/autoload.php";

class RequestHandlerTest extends \Rony539\PhpFramework\TestSystem{
	function setUp()
	{
		define("TEST_DB_PATH", __DIR__."/../testing.json");
	}

	function testSaveData(){

		$GLOBALS["jsonStringBody"] = '{"data":"i like kebab!"}';

		ob_start();
			$httpStatusCode = \Rony539\PhpFramework\Example\RequestHandler::saveData();
		$httpOutput = ob_get_clean();

		$this->assertEquals($httpStatusCode, 201);
		
		$hasRightForm = property_exists(json_decode($httpOutput), "id");

		$this->assertEquals($hasRightForm, true);

	}

	function testLoadDataById(){
		file_put_contents(TEST_DB_PATH, '{"data_67c551d7129c7":"i like kebab!"}');
		$GLOBALS["jsonStringBody"] = '{"id":"data_67c551d7129c7"}';
		ob_start();
			$httpStatusCode = \Rony539\PhpFramework\Example\RequestHandler::loadDataById();
		$httpOutput = ob_get_clean();

		$hasRightForm = property_exists(json_decode($httpOutput), "data");
		$this->assertEquals($hasRightForm, true);
		$this->assertEquals(json_decode($httpOutput)->data, "i like kebab!");
		$this->assertEquals($httpStatusCode, 200);

	}
}

new RequestHandlerTest();