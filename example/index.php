<?php

declare(strict_types=1);
namespace Rony539\PhpFramework\Example;

use Rony539\PhpFramework\Router;

require_once __DIR__ . "/../vendor/autoload.php";


Router::route(httpMethod: "GET", httpPath: "/ping", handler: function() {
	echo "pong!";
	return 200;
});

Router::route(httpMethod: "GET", httpPath: "/wrong", handler: function() {
	echo "hello!";	
	// Notice the missing return.
});

/* Router::dispatch(Toolkit::$requestedHttpMethod, Toolkit::$requestedHttpRoute); */
Router::server(ip: "127.0.0.1", port: 6969);
