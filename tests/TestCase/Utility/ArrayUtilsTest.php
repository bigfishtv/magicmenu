<?php
namespace MagicMenu\Test;

use MagicMenu\Utility\ArrayUtils;

use Cake\TestSuite\TestCase;

class ArrayUtilsTest extends TestCase
{
	public function testConsumeStringKey()
	{
		$array = [
			'one' => 1,
			'two' => 2,
			'three' => 3
		];

		$value = ArrayUtils::consume('two', $array);
		$expected = 2;
		$this->assertSame($expected, $value);

		$expected = [
			'one' => 1,
			'three' => 3
		];
		$this->assertSame($expected, $array);
	}

	public function testConsumeNumericKey()
	{
		$array = [
			0 => false,
			'true' => 'blue'
		];
		$value = ArrayUtils::consume(0, $array);
		$expected = false;
		$this->assertSame($expected, $value);

		$expected = [
			'true' => 'blue',
		];
		$this->assertSame($expected, $array);
	}

	public function testConsumeNonexistentKey()
	{
		$array = [
			'nothing'
		];
		$value = ArrayUtils::consume('undefined', $array);
		$this->assertNull($value);

		$expected = [
			'nothing',
		];
		$this->assertSame($expected, $array);
	}
}