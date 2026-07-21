<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\SanitizationHelperTrait;

use PHPUnit\Framework\TestCase;
use WordPressCS\WordPress\Helpers\SanitizationHelperTrait;

/**
 * Tests for the `SanitizationHelperTrait::is_sanitizing_and_unslashing_function()` utility method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\SanitizationHelperTrait::is_sanitizing_and_unslashing_function
 */
final class IsSanitizingAndUnslashingFunctionUnitTest extends TestCase {

	/**
	 * Test class using the SanitizationHelperTrait for testing purposes.
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
		self::$testClass = new class() {
			use SanitizationHelperTrait;
		};
	}

	/**
	 * Test is_sanitizing_and_unslashing_function().
	 *
	 * @dataProvider dataIsSanitizingAndUnslashingFunction
	 *
	 * @param string $functionName   The function name to test.
	 * @param bool   $expectedResult The expected return value.
	 *
	 * @return void
	 */
	public function testIsSanitizingAndUnslashingFunction( $functionName, $expectedResult ) {
		$this->assertSame(
			$expectedResult,
			self::$testClass->is_sanitizing_and_unslashing_function( $functionName )
		);
	}

	/**
	 * Data provider.
	 *
	 * @see testIsSanitizingAndUnslashingFunction()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsSanitizingAndUnslashingFunction() {
		return array(
			'lowercase_name' => array(
				'functionName'   => 'absint',
				'expectedResult' => true,
			),
			'mixedcase_name' => array(
				'functionName'   => 'iNtVaL',
				'expectedResult' => true,
			),
			'fully_qualified_name' => array(
				'functionName'   => '\boolval',
				'expectedResult' => true,
			),
			'not_a_sanitizing_and_unslashing_function' => array(
				'functionName'   => 'sanitize_text_field',
				'expectedResult' => false,
			),
		);
	}
}
