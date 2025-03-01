<?php
namespace Rony539\PhpFramework\Example;
require_once __DIR__."/../vendor/autoload.php";

$routerInstance = new \Rony539\PhpFramework\Router();

$routerInstance->route("GET", "/data/?", "\\Rony539\\PhpFramework\\Example\\RequestHandler::LoadDataById");

$routerInstance->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

