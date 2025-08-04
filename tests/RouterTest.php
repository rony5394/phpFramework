<?php
use PHPUnit\Framework\TestCase;
use Rony539\PhpFramework\Router;

class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        $refClass = new \ReflectionClass(Router::class);

        $routesProp = $refClass->getProperty('routes');
        $routesProp->setAccessible(true);
        $routesProp->setValue(null, []);

        $middlewaresProp = $refClass->getProperty('middlewares');
        $middlewaresProp->setAccessible(true);
        $middlewaresProp->setValue(null, []);
    }

    public function testRouteAndDispatch(){
	    Router::route("GET", "/test", function(){
	    	return 200;
	    });

	    $status = Router::dispatch("GET", "/test");
	    $this->assertEquals($status, 200);
    }

    public function testNotFound(){
	    Router::route("GET", "/test", function(){
		    return 200;
	    });
	    $status = Router::dispatch("GET", "/thisRouteShouldntExists");
	    $this->assertEquals($status, 404);

    }

    public function testMethodNotAllowed(){
	    Router::route("GET", "/test", function(){
		    return 200;
	    });
	    $status = Router::dispatch("POST", "/test");
	    $this->assertEquals($status, 405);
    
    }

    public function testMiddlewareIntercept(){
	    Router::middleware("TestMiddleware", function(){
	    	return 503;
	    });

	    Router::route("GET", "/test", function(){
		    return 200;
	    }, ["TestMiddleware"]);

	    $status = Router::dispatch("GET", "/test");
	    $this->assertEquals($status, 503);
    }
}

