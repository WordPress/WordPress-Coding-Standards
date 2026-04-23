<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\FormattingFunctionsHelper;

use PHPUnit\Framework\TestCase;
use WordPressCS\WordPress\Helpers\FormattingFunctionsHelper;

/**
 * Tests for the `FormattingFunctionsHelper::is_formatting_function()` method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\FormattingFunctionsHelper::is_formatting_function
 */
final class IsFormattingFunctionUnitTest extends TestCase {

	/**
	 * Test is_formatting_function().
	 *
	 * @dataProvider dataIsFormattingFunction
	 *
	 * @param string $functionName   The function name to test.
	 * @param bool   $expectedResult The expected return value.
	 *
	 * @return void
	 */
	public function testIsFormattingFunction( $functionName, $expectedResult ) {
		$this->assertSame(
			$expectedResult,
			FormattingFunctionsHelper::is_formatting_function( $functionName )
		);
	}

	/**
	 * Data provider.
	 *
	 * @see testIsFormattingFunction()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsFormattingFunction() {
		return array(
			'lowercase_name'            => array(
				'functionName'   => 'sprintf',
				'expectedResult' => true,
			),
			'mixedcase_name'            => array(
				'functionName'   => 'iMpLoDe',
				'expectedResult' => true,
			),
			'not_a_formatting_function' => array(
				'functionName'   => 'printf',
				'expectedResult' => false,
			),
		);
	}
}
