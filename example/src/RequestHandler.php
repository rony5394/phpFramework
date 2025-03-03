<?php

namespace Rony539\PhpFramework\Example;

class RequestHandler{
	static public function loadDataById(): int{
		$requestBody = json_decode($GLOBALS["jsonStringBody"]);
		if(!isset($requestBody->id))return 400;

		if(!is_file(TEST_DB_PATH))return 404;
		$dbData = json_decode(file_get_contents(TEST_DB_PATH));

		$requestedDataId = $requestBody->id;

		if(!isset($dbData->$requestedDataId))return 404;

		echo json_encode(["data"=> $dbData->$requestedDataId]);
		return 200;
	}

	static public function saveData(){
		$requestBody = json_decode($GLOBALS["jsonStringBody"]);

		if(!isset($requestBody->data))return 400;

		if(!is_file(TEST_DB_PATH)){
			file_put_contents(TEST_DB_PATH, "{}");
		}

		$dbData = json_decode(file_get_contents(TEST_DB_PATH));
		if($dbData == null){return 500;}

		$newDataId = uniqid("data_");

		$dbData->$newDataId = $requestBody->data;
		$isWriteSuccessfull = file_put_contents(TEST_DB_PATH, json_encode($dbData));

		if($isWriteSuccessfull == false)return 507;

		echo json_encode(["id"=> $newDataId]);
		return 201;
	}
}