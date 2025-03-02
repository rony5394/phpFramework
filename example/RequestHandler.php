<?php

namespace Rony539\PhpFramework\Example;

class RequestHandler{
	static public function LoadDataById(){
		$requestBody = json_decode(file_get_contents('php://input'));
		if(!isset($requestBody->id)){http_response_code(400);exit;}

		if(!is_file(TEST_DB_PATH)){http_response_code(404);exit;}
		$dbData = json_decode(file_get_contents(TEST_DB_PATH));

		$requestedDataId = $requestBody->id;

		if(!isset($dbData->$requestedDataId)){http_response_code(404);exit;}

		echo json_encode(["data"=> $dbData->$requestedDataId]);
	}

	static public function SaveData(){
		$requestBody = json_decode(file_get_contents('php://input'));
		if(!isset($requestBody->data)){http_response_code(400);exit;}

		if(!is_file(TEST_DB_PATH)){
			file_put_contents(TEST_DB_PATH, "{}");
		}

		$dbData = json_decode(file_get_contents(TEST_DB_PATH));
		if($dbData == null){http_response_code(500);exit;}

		$newDataId = uniqid("data_");

		$dbData->$newDataId = $requestBody->data;
		$isWriteSuccessfull = file_put_contents(TEST_DB_PATH, json_encode($dbData));

		if($isWriteSuccessfull == false){http_response_code(507);exit;}

		echo json_encode(["id"=> $newDataId]);

	}
}