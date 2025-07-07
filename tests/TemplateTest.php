<?php
declare(strict_types=1);

require_once __DIR__."/../vendor/autoload.php";
use \Rony539\PhpFramework\Template;

class TemplateTest extends \Rony539\PhpFramework\TestSystem {
	function setUp(){
		Template::AddTemplate("testTemplate", '
		<html>
			<head>@$title@</head>
			<body>
			@#if(date("H") <= 12)@
				<p>Good Morning</p>
			@#else@
				<p>Good Afternoon</p>
			@/if@
			<h3>Your Tasks</h3>
			@#each($items as $item)@
				<p>@$item@</p>
			@/each@
			<p>Your email: jonn@@example.com</p>
			</body>
		</html>
		');
	}
	
	function testRender(){
		Template::Render("testTemplate", ["items"=>["kebab", "capybara", "pomoc"], "title"=>"kebab"]);
	} 
}
new TemplateTest();
