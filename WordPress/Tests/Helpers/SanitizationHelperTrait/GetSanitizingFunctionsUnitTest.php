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
 * Tests for the `SanitizationHelperTrait::get_sanitizing_functions()` utility method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\SanitizationHelperTrait::get_sanitizing_functions
 */
final class GetSanitizingFunctionsUnitTest extends TestCase {

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
	 * Test that get_sanitizing_functions() returns an array containing a known
	 * sanitizing function.
	 *
	 * @return void
	 */
	public function testGetSanitizingFunctionsContainsKnownFunction() {
		$result = $this->testClass->get_sanitizing_functions();

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'sanitize_text_field', $result );
		$this->assertTrue( $result['sanitize_text_field'] );
	}

	/**
	 * Test that get_sanitizing_functions() includes custom sanitizing functions.
	 *
	 * @return void
	 */
	public function testGetSanitizingFunctionsIncludesCustomFunctions() {
		$defaultCount                               = count( $this->testClass->get_sanitizing_functions() );
		$this->testClass->customSanitizingFunctions = array( 'my_custom_sanitizer' );

		$result = $this->testClass->get_sanitizing_functions();

		$this->assertCount( $defaultCount + 1, $result );
		$this->assertArrayHasKey( 'my_custom_sanitizer', $result );
		$this->assertFalse( $result['my_custom_sanitizer'] );
	}

	/**
	 * Test that get_sanitizing_functions() updates the result when custom
	 * sanitizing functions are changed.
	 *
	 * @return void
	 */
	public function testGetSanitizingFunctionsUpdatesWhenCustomFunctionsChange() {
		$this->testClass->customSanitizingFunctions = array( 'first_custom' );
		$result                                     = $this->testClass->get_sanitizing_functions();
		$this->assertArrayHasKey( 'first_custom', $result );
		$this->assertFalse( $result['first_custom'] );

		$this->testClass->customSanitizingFunctions = array( 'second_custom' );
		$result                                     = $this->testClass->get_sanitizing_functions();
		$this->assertArrayHasKey( 'second_custom', $result );
		$this->assertFalse( $result['second_custom'] );
		$this->assertArrayNotHasKey( 'first_custom', $result );
	}
}
