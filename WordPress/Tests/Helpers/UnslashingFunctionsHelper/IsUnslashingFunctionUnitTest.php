<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\UnslashingFunctionsHelper;

use PHPUnit\Framework\TestCase;
use WordPressCS\WordPress\Helpers\UnslashingFunctionsHelper;

/**
 * Tests for the `UnslashingFunctionsHelper::is_unslashing_function()` method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\UnslashingFunctionsHelper::is_unslashing_function
 */
final class IsUnslashingFunctionUnitTest extends TestCase {

	/**
	 * Test is_unslashing_function().
	 *
	 * @dataProvider dataIsUnslashingFunction
	 *
	 * @param string $functionName   The function name to test.
	 * @param bool   $expectedResult The expected return value.
	 *
	 * @return void
	 */
	public function testIsUnslashingFunction( $functionName, $expectedResult ) {
		$this->assertSame(
			$expectedResult,
			UnslashingFunctionsHelper::is_unslashing_function( $functionName )
		);
	}

	/**
	 * Data provider.
	 *
	 * @see testIsUnslashingFunction()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsUnslashingFunction() {
		return array(
			'lowercase_name'             => array(
				'functionName'   => 'wp_unslash',
				'expectedResult' => true,
			),
			'mixedcase_name'             => array(
				'functionName'   => 'sTrIpSlAsHeS_DeEp',
				'expectedResult' => true,
			),
			'not_an_unslashing_function' => array(
				'functionName'   => 'stripslashes',
				'expectedResult' => false,
			),
		);
	}
}
