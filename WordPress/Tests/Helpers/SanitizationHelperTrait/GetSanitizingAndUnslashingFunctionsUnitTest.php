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
 * Tests for the `SanitizationHelperTrait::get_sanitizing_and_unslashing_functions()` utility method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\SanitizationHelperTrait::get_sanitizing_and_unslashing_functions
 */
final class GetSanitizingAndUnslashingFunctionsUnitTest extends TestCase {

	/**
	 * Test class using the SanitizationHelperTrait for testing purposes.
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
			use SanitizationHelperTrait;
		};
	}

	/**
	 * Test that get_sanitizing_and_unslashing_functions() returns an array containing a known
	 * sanitizing and unslashing function.
	 *
	 * @return void
	 */
	public function testGetSanitizingAndUnslashingFunctionsContainsKnownFunction() {
		$result = $this->testClass->get_sanitizing_and_unslashing_functions();

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'absint', $result );
		$this->assertTrue( $result['absint'] );
	}

	/**
	 * Test that get_sanitizing_and_unslashing_functions() includes custom unslashing sanitizing functions.
	 *
	 * @return void
	 */
	public function testGetSanitizingAndUnslashingFunctionsIncludesCustomFunctions() {
		$defaultCount = count( $this->testClass->get_sanitizing_and_unslashing_functions() );
		$this->testClass->customUnslashingSanitizingFunctions = array( 'my_custom_unslasher' );

		$result = $this->testClass->get_sanitizing_and_unslashing_functions();

		$this->assertCount( $defaultCount + 1, $result );
		$this->assertArrayHasKey( 'my_custom_unslasher', $result );
		$this->assertFalse( $result['my_custom_unslasher'] );
	}

	/**
	 * Test that get_sanitizing_and_unslashing_functions() updates the result when custom
	 * unslashing sanitizing functions are changed.
	 *
	 * @return void
	 */
	public function testGetSanitizingAndUnslashingFunctionsUpdatesWhenCustomFunctionsChange() {
		$this->testClass->customUnslashingSanitizingFunctions = array( 'first_custom' );
		$result = $this->testClass->get_sanitizing_and_unslashing_functions();
		$this->assertArrayHasKey( 'first_custom', $result );
		$this->assertFalse( $result['first_custom'] );

		$this->testClass->customUnslashingSanitizingFunctions = array( 'second_custom' );
		$result = $this->testClass->get_sanitizing_and_unslashing_functions();
		$this->assertArrayHasKey( 'second_custom', $result );
		$this->assertFalse( $result['second_custom'] );
		$this->assertArrayNotHasKey( 'first_custom', $result );
	}
}
