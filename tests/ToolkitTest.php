<?php

// THIS TEST IS WRITEN BY GPT
use PHPUnit\Framework\TestCase;
use Rony539\PhpFramework\Toolkit;

class ToolkitTest extends TestCase
{
	protected function setUp(): void
	{
		Toolkit::$requestedHttpMethod = '';
		Toolkit::$requestedHttpRoute = '';
		Toolkit::$requestJsonBodyParsed = (object)[];
	}

	public function testCheckObjectFormReturnsTrue()
	{
		$required = ['name' => 'string', 'age' => 'integer'];
		$data = ['name' => 'Alice', 'age' => 30];

		$this->assertTrue(Toolkit::checkObjectForm($required, $data));
	}

	public function testCheckObjectFormReturnsFalseWhenMissingKey()
	{
		$required = ['name' => 'string', 'age' => 'integer'];
		$data = ['name' => 'Alice'];

		$this->assertFalse(Toolkit::checkObjectForm($required, $data));
	}

	public function testCheckBodyFormWithJsonParsed()
	{
		Toolkit::$requestJsonBodyParsed = (object)[
			'email' => 'test@example.com',
			'active' => true
		];

		$required = ['email' => 'string', 'active' => 'boolean'];

		$this->assertTrue(Toolkit::checkBodyForm($required));
	}

	public function testCheckObjectFormWithNegativeType()
	{
		$required = ['id' => '!string'];
		$data = ['id' => 123];

		$this->assertTrue(Toolkit::checkObjectForm($required, $data));
		$this->assertFalse(Toolkit::checkObjectForm(['id' => '!integer'], $data));
	}
}

