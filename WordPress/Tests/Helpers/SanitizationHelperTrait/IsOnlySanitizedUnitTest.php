<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\SanitizationHelperTrait;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use WordPressCS\WordPress\Helpers\SanitizationHelperTrait;

/**
 * Tests for the `SanitizationHelperTrait::is_only_sanitized()` utility method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\SanitizationHelperTrait::is_only_sanitized
 */
final class IsOnlySanitizedUnitTest extends UtilityMethodTestCase {

	/**
	 * Test class using the SanitizationHelperTrait for testing purposes.
	 *
	 * @var object
	 */
	private static $testClass;

	/**
	 * Set up the test class.
	 *
	 * @beforeClass
	 *
	 * @return void
	 */
	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();

		self::$testClass = new class() {
			use SanitizationHelperTrait;
		};
	}

	/**
	 * Test is_only_sanitized() returns false if the token does not exist.
	 *
	 * @return void
	 */
	public function testIsOnlySanitizedReturnsFalseIfTokenDoesNotExist() {
		$this->assertFalse( self::$testClass->is_only_sanitized( self::$phpcsFile, -1 ) );
	}

	/**
	 * Test is_only_sanitized().
	 *
	 * @dataProvider dataIsOnlySanitized
	 *
	 * @param string     $testMarker     The comment which prefaces the target token in the test file.
	 * @param bool       $expectedResult The expected return value.
	 * @param int|string $tokenType      The token type to search for. Defaults to T_VARIABLE.
	 *
	 * @return void
	 */
	public function testIsOnlySanitized( $testMarker, $expectedResult, $tokenType = \T_VARIABLE ) {
		$stackPtr = $this->getTargetToken( $testMarker, $tokenType );
		$result   = self::$testClass->is_only_sanitized(
			self::$phpcsFile,
			$stackPtr
		);

		$this->assertSame( $expectedResult, $result );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsOnlySanitized()
	 *
	 * @return array<string, array<string, bool|int|string>>
	 */
	public static function dataIsOnlySanitized() {
		return array(
			// Cases where false should be returned.
			'not_sanitized_echo' => array(
				'testMarker'     => '/* testNotSanitizedEcho */',
				'expectedResult' => false,
			),
			'inner_function_not_sanitizing' => array(
				'testMarker'     => '/* testInnerFunctionNotSanitizing */',
				'expectedResult' => false,
			),
			'only_unslashed' => array(
				'testMarker'     => '/* testOnlyUnslashed */',
				'expectedResult' => false,
			),
			'cast_nested_in_function' => array(
				'testMarker'     => '/* testCastNestedInFunction */',
				'expectedResult' => false,
			),
			'sanitized_nested_in_function' => array(
				'testMarker'     => '/* testSanitizedNestedInFunction */',
				'expectedResult' => false,
			),

			// Cases where true should be returned.
			'single_sanitizing_function' => array(
				'testMarker'     => '/* testSingleSanitizingFunction */',
				'expectedResult' => true,
			),
			'unslashing_sanitizing_function' => array(
				'testMarker'     => '/* testUnslashingSanitizingFunction */',
				'expectedResult' => true,
			),
			'array_walking_sanitizing_callback' => array(
				'testMarker'     => '/* testArrayWalkingSanitizingCallback */',
				'expectedResult' => true,
			),
			'in_unset' => array(
				'testMarker'     => '/* testInUnset */',
				'expectedResult' => true,
			),
			'safe_cast' => array(
				'testMarker'     => '/* testSafeCast */',
				'expectedResult' => true,
			),
			'string_token_sanitized' => array(
				'testMarker'     => '/* testStringTokenSanitized */',
				'expectedResult' => true,
				'tokenType'      => \T_STRING,
			),
		);
	}
}
