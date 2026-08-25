<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\EscapingFunctionsTrait;

use PHPUnit\Framework\TestCase;
use WordPressCS\WordPress\Helpers\EscapingFunctionsTrait;

/**
 * Tests for the `EscapingFunctionsTrait::is_auto_escaped_function()` method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\EscapingFunctionsTrait::is_auto_escaped_function
 */
final class IsAutoEscapedFunctionUnitTest extends TestCase {

	/**
	 * Test class using the EscapingFunctionsTrait for testing purposes.
	 *
	 * @var object
	 */
	private $testClass;

	/**
	 * Set up the test class for each test.
	 *
	 * @before
	 *
	 * @return void
	 */
	protected function setUp(): void {
		$this->testClass = new class() {
			use EscapingFunctionsTrait;
		};
	}

	/**
	 * Test is_auto_escaped_function() with default auto escaped functions.
	 *
	 * @dataProvider dataIsAutoEscapedFunction
	 *
	 * @param string $functionName   The function name to test.
	 * @param bool   $expectedResult The expected return value.
	 *
	 * @return void
	 */
	public function testIsAutoEscapedFunction( $functionName, $expectedResult ) {
		$this->assertSame(
			$expectedResult,
			$this->testClass->is_auto_escaped_function( $functionName )
		);
	}

	/**
	 * Data provider.
	 *
	 * @see testIsAutoEscapedFunction()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsAutoEscapedFunction() {
		return array(
			'lowercase_name'               => array(
				'functionName'   => 'bloginfo',
				'expectedResult' => true,
			),
			'mixedcase_name'               => array(
				'functionName'   => 'bOdY_ClAsS',
				'expectedResult' => true,
			),
			'not_an_auto_escaped_function' => array(
				'functionName'   => 'esc_html',
				'expectedResult' => false,
			),
		);
	}

	/**
	 * Test that a custom auto-escaped function is recognized.
	 *
	 * @return void
	 */
	public function testCustomAutoEscapedFunctionIsRecognized() {
		$this->assertFalse( $this->testClass->is_auto_escaped_function( 'my_custom_auto_escaped' ) );
		$this->testClass->customAutoEscapedFunctions = array( 'my_custom_auto_escaped' );
		$this->assertTrue( $this->testClass->is_auto_escaped_function( 'my_custom_auto_escaped' ) );
		$this->assertTrue( $this->testClass->is_auto_escaped_function( 'bloginfo' ) );
	}

	/**
	 * Test that the auto-escaped function list is updated when the custom
	 * auto-escaped functions are changed.
	 *
	 * @return void
	 */
	public function testIsAutoEscapedFunctionUpdatesWhenCustomFunctionsChange() {
		$this->testClass->customAutoEscapedFunctions = array( 'first_custom' );
		$this->assertTrue( $this->testClass->is_auto_escaped_function( 'first_custom' ) );

		$this->testClass->customAutoEscapedFunctions = array( 'second_custom' );
		$this->assertTrue( $this->testClass->is_auto_escaped_function( 'second_custom' ) );
		$this->assertFalse( $this->testClass->is_auto_escaped_function( 'first_custom' ) );
	}
}
