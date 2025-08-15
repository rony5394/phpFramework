<?php

use PHPUnit\Framework\TestCase;
use Rony539\PhpFramework\Template;

define("TEST_TEMPLATE", __DIR__."/TemplateAssets/testTemplate.html");
define("EXPECTED_OUTPUT_FILE", __DIR__."/TemplateAssets/expectedOutput.html");
define("EXPECTED_EXECUTION_FILE", __DIR__."/TemplateAssets/expectedExecutionOutput.html");
define("OUTPUT_FILE", __DIR__."/../.cache/template_40bdc9333df2560a3293ea80586e936869608add54f18aa4083feeccfbfaaa10");

class TemplateTest extends TestCase{
	protected function setUp(): void
	{
		$template = new \ReflectionClass(Template::class);
		$templatesProp = $template->getProperty("templates");
		$templatesProp->setAccessible(true);
		$templatesProp->setValue(null, []);
	}

	public function testRender() {
		Template::AddTemplateFromFile("testTemplate", TEST_TEMPLATE);
		ob_start();
		Template::Render("testTemplate", ["title"=>"ThisIsTitle", "items"=>["never", "gona", "give"]], "AUTODETECT");
		
		$executedOutput = ob_get_clean();

		$output = file_get_contents(OUTPUT_FILE);
		$expectedOutput =  file_get_contents(EXPECTED_OUTPUT_FILE);
		$this->assertEquals($output, $expectedOutput);

		$predictedExecutedOutput = date("H") <= 12 ? str_replace("<p>Good Afternoon</p>", "<p>Good Morning</p>", file_get_contents(EXPECTED_EXECUTION_FILE)) : file_get_contents(EXPECTED_EXECUTION_FILE);
		$this->assertEquals($executedOutput, $predictedExecutedOutput);
	}
}
