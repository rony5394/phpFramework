<?php
namespace Rony539\PhpFramework\Example;
require_once __DIR__."/../../vendor/autoload.php";

use \Rony539\PhpFramework\Toolkit;

class Middlewares{
	static public function statelessAuth(){
		$hasValidForm = Toolkit::checkObjectForm(["password"=>"string"], Toolkit::$requestJsonBodyParsed);

		if(!$hasValidForm)return 400;

		// If you don't want to use toolkit you can use php://input
		if(Toolkit::$requestJsonBodyParsed->password != SECRET_PASSWORD) return 401;

		return;
	}
}