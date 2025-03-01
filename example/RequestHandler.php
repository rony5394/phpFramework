<?php

namespace Rony539\PhpFramework\Example;

class RequestHandler{
	static public function LoadDataById(){
		$requestBody = json_decode(file_get_contents('php://input'));
		$requestedDataId = $requestBody["id"];

		if(!isset($requestedDataId)){http_response_code(400);exit;}
		if(!isset($testDbData[$requestedDataId])){http_response_code(404);exit;};

		$testDbData = json_decode(file_get_contents(__DIR__."/../testing.json"));

		echo json_encode(["data"=>$testDbData[$requestedDataId]]);
	}
}