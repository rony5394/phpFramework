<?php
namespace Rony539\PhpFramework;
require_once __DIR__."/../vendor/autoload.php";

abstract class TestSystem{
	final function __construct(){
		echo "Running TestSystem...\n";

		$this->setUp();
		foreach(get_class_methods($this) as $test){
			if(!str_starts_with($test, "test"))continue;
			echo "\e[36m[RUNNING]\e[0m $test is in progress...";
			@$this->$test();
			echo "\r\e[32m[PASS]\e[0m $test completed successfully.\n";
		}
	}

	function assertEquals($a, $b){
		if($a == $b)return;
		echo "                                                  ";
		echo "\r\e[31m[FAIL]\e[0m ". var_export($a, return:true) ." != ".var_export($b, return:true);
		exit(1);
	}

	abstract function setUp();
}