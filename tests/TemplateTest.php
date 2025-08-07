<?php
use PHPUnit\Framework\TestCase;
use Rony539\PhpFramework\Template;

class TemplateTest extends TestCase{
	protected function setUp(): void
	{
		$template = new \ReflectionClass(Template::class);
		$templatesProp = $template->getProperty("templates");
		$templatesProp->setAccessible(true);
		$templatesProp->setValue(null, []);
	}

	public function testRender(){
		Template::AddTemplateFromFile("testTemplate", __DIR__."/testTemplate");
		ob_start();
		Template::Render("testTemplate", ["title"=>"ThisIsTitle", "items"=>["never", "gona", "give"]], "AUTODETECT"); 

		$compiledOutput = file_get_contents(__DIR__."/../.cache/template_40bdc9333df2560a3293ea80586e936869608add54f18aa4083feeccfbfaaa10");
		$predictedOutput = file_get_contents(__DIR__."/testTemplateCompiled");
		$this->assertEquals($compiledOutput, $predictedOutput);

		$executedOutput = ob_get_clean();

		$predictedExecutedOutput = file_get_contents(__DIR__."/testTemplateExecuted");
		$this->assertEquals($executedOutput, $predictedExecutedOutput);
	}

}
