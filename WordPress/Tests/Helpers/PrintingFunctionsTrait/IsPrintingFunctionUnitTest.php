<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\PrintingFunctionsTrait;

use PHPUnit\Framework\TestCase;
use WordPressCS\WordPress\Helpers\PrintingFunctionsTrait;

/**
 * Tests for the `PrintingFunctionsTrait::is_printing_function()` method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\PrintingFunctionsTrait::is_printing_function
 */
final class IsPrintingFunctionUnitTest extends TestCase {

	/**
	 * Test class using the PrintingFunctionsTrait for testing purposes.
	 *
	 * @var object
	 */
	private static $testClass;

	/**
	 * Set up the test class.
	 *
	 * @return void
	 */
	public static function setUpBeforeClass(): void {
		self::$testClass                          = new class() {
			use PrintingFunctionsTrait;
		};
		self::$testClass->customPrintingFunctions = array( 'my_custom_function' );
	}

	/**
	 * Test is_printing_function().
	 *
	 * @dataProvider dataIsPrintingFunction
	 *
	 * @param string $functionName   The function name to test.
	 * @param bool   $expectedResult The expected return value.
	 *
	 * @return void
	 */
	public function testIsPrintingFunction( $functionName, $expectedResult ) {
		$this->assertSame(
			$expectedResult,
			self::$testClass->is_printing_function( $functionName )
		);
	}

	/**
	 * Data provider.
	 *
	 * @see testIsPrintingFunction()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsPrintingFunction() {
		return array(
			'not_a_printing_function' => array(
				'functionName'   => 'echo',
				'expectedResult' => false,
			),
			'lowercase_name' => array(
				'functionName'   => 'printf',
				'expectedResult' => true,
			),
			'mixedcase_name' => array(
				'functionName'   => 'vPrInTf',
				'expectedResult' => true,
			),
			'custom_printing_function' => array(
				'functionName'   => 'my_custom_function',
				'expectedResult' => true,
			),
		);
	}
}
