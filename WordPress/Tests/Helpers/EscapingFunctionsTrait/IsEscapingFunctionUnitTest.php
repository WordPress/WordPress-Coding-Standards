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
 * Tests for the `EscapingFunctionsTrait::is_escaping_function()` method.
 *
 * @since 3.4.2
 *
 * @covers \WordPressCS\WordPress\Helpers\EscapingFunctionsTrait::is_escaping_function
 */
final class IsEscapingFunctionUnitTest extends TestCase {

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
	 * Test is_escaping_function() with default escaping functions.
	 *
	 * @dataProvider dataIsEscapingFunction
	 *
	 * @param string $functionName   The function name to test.
	 * @param bool   $expectedResult The expected return value.
	 *
	 * @return void
	 */
	public function testIsEscapingFunction( $functionName, $expectedResult ) {
		$this->assertSame(
			$expectedResult,
			$this->testClass->is_escaping_function( $functionName )
		);
	}

	/**
	 * Data provider.
	 *
	 * @see testIsEscapingFunction()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsEscapingFunction() {
		return array(
			'lowercase_name'           => array(
				'functionName'   => 'esc_html',
				'expectedResult' => true,
			),
			'mixedcase_name'           => array(
				'functionName'   => 'eSc_AtTr',
				'expectedResult' => true,
			),
			'not_an_escaping_function' => array(
				'functionName'   => 'printf',
				'expectedResult' => false,
			),
		);
	}

	/**
	 * Test that a custom escaping function is recognized.
	 *
	 * @return void
	 */
	public function testCustomEscapingFunctionIsRecognized() {
		$this->assertFalse(
			$this->testClass->is_escaping_function( 'my_custom_escape' ),
			'Custom escaping function is recognized even though not set'
		);

		$this->testClass->customEscapingFunctions = array( 'my_custom_escape' );
		$this->assertTrue(
			$this->testClass->is_escaping_function( 'my_custom_escape' ),
			'Custom escaping function is not recognized when set'
		);
		$this->assertTrue(
			$this->testClass->is_escaping_function( 'esc_html' ),
			'WP native escaping function is not recognized when custom functions are set'
		);
	}

	/**
	 * Test that the escaping function list is updated when the custom escaping
	 * functions are changed.
	 *
	 * @return void
	 */
	public function testIsEscapingFunctionUpdatesWhenCustomFunctionsChange() {
		$this->testClass->customEscapingFunctions = array( 'first_custom' );
		$this->assertTrue(
			$this->testClass->is_escaping_function( 'first_custom' ),
			'Custom escaping function is not recognized when set (first)'
		);

		$this->testClass->customEscapingFunctions = array( 'second_custom' );
		$this->assertTrue(
			$this->testClass->is_escaping_function( 'second_custom' ),
			'Custom escaping function is not recognized when set (second)'
		);
		$this->assertFalse(
			$this->testClass->is_escaping_function( 'first_custom' ),
			'Custom escaping function is still recognized while it should have been removed (first)'
		);
	}
}
