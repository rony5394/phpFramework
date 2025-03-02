<?php

namespace Rony539\PhpFramework\Example;

class RequestHandler{
	static public function LoadDataById(){
		$requestBody = json_decode(file_get_contents('php://input'));

		if(!isset($requestBody->id)){http_response_code(400);exit;}

		if(!is_file(TEST_DB_PATH)){http_response_code(404);exit;}

		$requestedDataId = $requestBody->id;

		if(!isset($testDbData[$requestedDataId])){http_response_code(404);exit;};

		$testDbData = json_decode(file_get_contents(TEST_DB_PATH));

		echo json_encode(["data"=>$testDbData[$requestedDataId]]);
	}

	static public function SaveData(){
		$requestBody = json_decode(file_get_contents('php://input'));	
		if(!isset($requestBody->data)){http_response_code(400);exit;}

		if(!is_file(TEST_DB_PATH)){
			file_put_contents(TEST_DB_PATH, "{}");
		}

		$newDataId = uniqid(more_entropy: true);

		$dbData = json_decode(file_get_contents(TEST_DB_PATH));

		$isWriteSuccessfull = file_put_contents(TEST_DB_PATH,
			
			[$newDataId] = $requestBody->data, LOCK_EX
		);

		if(!$isWriteSuccessfull){http_response_code(507);exit;}

		echo json_encode(["id"=>$newDataId]);
	}
}