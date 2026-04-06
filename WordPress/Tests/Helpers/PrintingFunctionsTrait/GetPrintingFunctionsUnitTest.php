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
		};
	}

	/**
	 * Test that get_printing_functions() returns an array containing a known
	 * built-in printing function.
	 *
	 * @return void
	 */
	public function testGetPrintingFunctionsContainsKnownFunction() {
		$result = $this->testClass->get_printing_functions();

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'printf', $result );
		$this->assertTrue( $result['printf'] );
	}

	/**
	 * Test that get_printing_functions() includes custom printing functions.
	 *
	 * @return void
	 */
	public function testGetPrintingFunctionsIncludesCustomFunctions() {
		$defaultCount                             = count( $this->testClass->get_printing_functions() );
		$this->testClass->customPrintingFunctions = array( 'my_custom_print' );

		$result = $this->testClass->get_printing_functions();

		$this->assertCount( $defaultCount + 1, $result );
		$this->assertArrayHasKey( 'my_custom_print', $result );
		$this->assertFalse( $result['my_custom_print'] );
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
		$this->assertArrayHasKey( 'first_custom', $result );
		$this->assertFalse( $result['first_custom'] );

		$this->testClass->customPrintingFunctions = array( 'second_custom' );
		$result                                   = $this->testClass->get_printing_functions();
		$this->assertArrayHasKey( 'second_custom', $result );
		$this->assertFalse( $result['second_custom'] );
		$this->assertArrayNotHasKey( 'first_custom', $result );
	}
}
