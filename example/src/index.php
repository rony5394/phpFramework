<?php
namespace Rony539\PhpFramework\Example;
require_once __DIR__."/../../vendor/autoload.php";

define("TEST_DB_PATH", __DIR__."/../testing.json");

use \Rony539\PhpFramework\Toolkit;

$routerInstance = new \Rony539\PhpFramework\Router();
Toolkit::init();

$routerInstance->route("GET", "/data", "\\Rony539\\PhpFramework\\Example\\RequestHandler::loadDataById");
$routerInstance->route("POST", "/data", "\\Rony539\\PhpFramework\\Example\\RequestHandler::saveData");

$responseCode = $routerInstance->dispatch(Toolkit::$requestedHttpMethod, Toolkit::$requestedHttpRoute);

http_response_code($responseCode);