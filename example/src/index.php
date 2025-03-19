<?php
namespace Rony539\PhpFramework\Example;
require_once __DIR__."/../../vendor/autoload.php";

define("TEST_DB_PATH", __DIR__."/../testing.json");
define("SECRET_PASSWORD", "12345");

use \Rony539\PhpFramework\Toolkit;

$routerInstance = new \Rony539\PhpFramework\Router();
Toolkit::init();

$routerInstance->middleware("SimpleAuth", "\Rony539\PhpFramework\Example\Middlewares::statelessAuth");

$routerInstance->route("GET", "/data", "\\Rony539\\PhpFramework\\Example\\RequestHandler::loadDataById");
$routerInstance->route("POST", "/data", "\\Rony539\\PhpFramework\\Example\\RequestHandler::saveData");
$routerInstance->route("GET", "/secretData", "\\Rony539\\PhpFramework\\Example\\RequestHandler::loadSecretData",["SimpleAuth"]);

$responseCode = $routerInstance->dispatch(Toolkit::$requestedHttpMethod, Toolkit::$requestedHttpRoute);

http_response_code($responseCode);