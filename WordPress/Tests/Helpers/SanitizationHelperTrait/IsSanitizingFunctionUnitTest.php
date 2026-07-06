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
 * Tests for the `SanitizationHelperTrait::is_sanitizing_function()` utility method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\SanitizationHelperTrait::is_sanitizing_function
 */
final class IsSanitizingFunctionUnitTest extends TestCase {

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
	 * Test is_sanitizing_function().
	 *
	 * @dataProvider dataIsSanitizingFunction
	 *
	 * @param string $functionName   The function name to test.
	 * @param bool   $expectedResult The expected return value.
	 *
	 * @return void
	 */
	public function testIsSanitizingFunction( $functionName, $expectedResult ) {
		$this->assertSame(
			$expectedResult,
			self::$testClass->is_sanitizing_function( $functionName )
		);
	}

	/**
	 * Data provider.
	 *
	 * @see testIsSanitizingFunction()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsSanitizingFunction() {
		return array(
			'lowercase_name'            => array(
				'functionName'   => 'sanitize_text_field',
				'expectedResult' => true,
			),
			'mixedcase_name'            => array(
				'functionName'   => 'SaNiTiZe_EmAiL',
				'expectedResult' => true,
			),
			'not_a_sanitizing_function' => array(
				'functionName'   => 'printf',
				'expectedResult' => false,
			),
		);
	}
}
