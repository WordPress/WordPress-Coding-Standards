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
 * Tests for the `PrintingFunctionsTrait::get_printing_functions()` method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\PrintingFunctionsTrait::get_printing_functions
 */
final class GetPrintingFunctionsUnitTest extends TestCase {

	/**
	 * Test class using the PrintingFunctionsTrait for testing purposes.
	 *
	 * @var object
	 */
	private $testClass;

	/**
	 * Set up the test class.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		$this->testClass = new class() {
			use PrintingFunctionsTrait;

			/**
			 * Retrieve the number of default printing functions.
			 *
			 * @return int
			 */
			public function get_default_function_count() {
				return count( $this->printingFunctions );
			}
		};
	}

	/**
	 * Test get_printing_functions() when no custom printing functions have been added.
	 *
	 * @return void
	 */
	public function testGetPrintingFunctionsWithoutCustomFunctions() {
		$result = $this->testClass->get_printing_functions();

		$this->assertIsArray( $result, 'Function list is not an array' );
		$this->assertCount( $this->testClass->get_default_function_count(), $result, 'Number of default printing functions does not match' );
		$this->assertArrayHasKey( 'printf', $result, 'printf() is not in the function list' );
		$this->assertTrue( $result['printf'], 'printf() value should be true, indicating a default function' );
	}

	/**
	 * Test that get_printing_functions() includes custom printing functions.
	 *
	 * @return void
	 */
	public function testGetPrintingFunctionsIncludesCustomFunctions() {
		$this->testClass->customPrintingFunctions = array( 'my_custom_print' );

		$result = $this->testClass->get_printing_functions();

		$this->assertCount( $this->testClass->get_default_function_count() + 1, $result, 'Total count should be default functions + 1 custom function' );
		$this->assertArrayHasKey( 'my_custom_print', $result, 'Custom function is not in the function list' );
		$this->assertFalse( $result['my_custom_print'], 'my_custom_print() value should be false, indicating a custom function' );
	}

	/**
	 * Test that get_printing_functions() updates the result when custom
	 * printing functions are changed.
	 *
	 * @return void
	 */
	public function testGetPrintingFunctionsUpdatesWhenCustomFunctionsChange() {
		$this->testClass->customPrintingFunctions = array( 'first_custom' );
		$result                                   = $this->testClass->get_printing_functions();
		$this->assertArrayHasKey( 'first_custom', $result, 'first_custom() is not in the function list' );
		$this->assertFalse( $result['first_custom'], 'first_custom() value should be false, indicating a custom function' );

		$this->testClass->customPrintingFunctions = array( 'second_custom' );
		$result                                   = $this->testClass->get_printing_functions();
		$this->assertArrayHasKey( 'second_custom', $result, 'second_custom() is not in the function list' );
		$this->assertFalse( $result['second_custom'], 'second_custom() value should be false, indicating a custom function' );
		$this->assertArrayNotHasKey( 'first_custom', $result, 'first_custom() should no longer be in the function list' );
	}
}
