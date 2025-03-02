<?php
namespace Rony539\PhpFramework\Example;
require_once __DIR__."/../vendor/autoload.php";

define("TEST_DB_PATH", __DIR__."/../vendor/autoload.php");

set_exception_handler(function ($exception) {
    http_response_code(500);
    die("Internal Server Error: $exception");
});

$routerInstance = new \Rony539\PhpFramework\Router();

$routerInstance->route("GET", "/data", "\\Rony539\\PhpFramework\\Example\\RequestHandler::LoadDataById");
$routerInstance->route("POST", "/data", "\Rony539\PhpFramework\\Example\RequestHandler::SaveData");

$routerInstance->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);