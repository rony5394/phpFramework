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

		$compiledOutput = file_get_contents(__DIR__."/../.cache/template_8432e3e17c4d16b1f4502cff1fd225850c116c9e");
		$predictedOutput = file_get_contents(__DIR__."/testTemplateCompiled");
		$this->assertEquals($compiledOutput, $predictedOutput);

		$executedOutput = ob_get_clean();

		$predictedExecutedOutput = file_get_contents(__DIR__."/testTemplateExecuted");
		$this->assertEquals($executedOutput, $predictedExecutedOutput);
	}

}
