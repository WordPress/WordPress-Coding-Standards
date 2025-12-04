<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\SanitizationHelperTrait;

use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use WordPressCS\WordPress\Helpers\SanitizationHelperTrait;

/**
 * Tests for the `SanitizationHelperTrait::is_sanitized()` utility method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\SanitizationHelperTrait::is_sanitized
 */
final class IsSanitizedUnitTest extends UtilityMethodTestCase {

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
	 * Test is_sanitized() returns false if the token does not exist.
	 *
	 * @return void
	 */
	public function testIsSanitizedReturnsFalseIfTokenDoesNotExist() {
		$this->assertFalse( self::$testClass->is_sanitized( self::$phpcsFile, -1 ) );
	}

	/**
	 * Test is_sanitized().
	 *
	 * @dataProvider dataIsSanitized
	 *
	 * @param string     $testMarker     The comment which prefaces the target token in the test file.
	 * @param bool       $expectedResult The expected return value.
	 * @param int|string $tokenType      The token type to search for. Defaults to T_VARIABLE.
	 *
	 * @return void
	 */
	public function testIsSanitized( $testMarker, $expectedResult, $tokenType = \T_VARIABLE ) {
		$stackPtr = $this->getTargetToken( $testMarker, $tokenType );
		$result   = self::$testClass->is_sanitized( self::$phpcsFile, $stackPtr );

		$this->assertSame( $expectedResult, $result );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsSanitized()
	 *
	 * @return array<string, array<string, bool|int|string>>
	 */
	public static function dataIsSanitized() {
		$phpcs_version = Helper::getVersion();
		$is_phpcs_4    = version_compare( $phpcs_version, '3.99.99', '>' );

		return array(
			// Cases where false should be returned.
			'not_within_function_call' => array(
				'testMarker'     => '/* testNotWithinFunctionCall */',
				'expectedResult' => false,
			),
			'non_sanitizing_function' => array(
				'testMarker'     => '/* testNonSanitizingFunction */',
				'expectedResult' => false,
			),
			'inner_function_not_sanitizing' => array(
				'testMarker'     => '/* testInnerFunctionNotSanitizing */',
				'expectedResult' => false,
				'tokenType'      => \T_STRING,
			),
			'unslashed_in_non_sanitizing_function' => array(
				'testMarker'     => '/* testUnslashedInNonSanitizingFunction */',
				'expectedResult' => false,
			),
			'partially_qualified' => array(
				'testMarker'     => '/* testPartiallyQualified */',
				'expectedResult' => false,
			),
			'fully_qualified_namespaced' => array(
				'testMarker'     => '/* testFullyQualifiedNamespaced */',
				'expectedResult' => false,
			),
			'namespace_relative' => array(
				'testMarker'     => '/* testNamespaceRelative */',
				'expectedResult' => false,
			),
			'namespace_relative_sub' => array(
				'testMarker'     => '/* testNamespaceRelativeSub */',
				'expectedResult' => false,
			),
			'method_call' => array(
				'testMarker'     => '/* testMethodCall */',
				'expectedResult' => false,
			),
			'static_method_call' => array(
				'testMarker'     => '/* testStaticMethodCall */',
				'expectedResult' => false,
			),
			'array_walking_non_sanitizing_callback' => array(
				'testMarker'     => '/* testArrayWalkingNonSanitizingCallback */',
				'expectedResult' => false,
			),
			'array_walking_non_string_callback' => array(
				'testMarker'     => '/* testArrayWalkingNonStringCallback */',
				'expectedResult' => false,
			),
			'array_walking_missing_callback' => array(
				'testMarker'     => '/* testArrayWalkingMissingCallback */',
				'expectedResult' => false,
			),

			// Cases where true should be returned.
			'in_unset' => array(
				'testMarker'     => '/* testInUnset */',
				'expectedResult' => true,
			),
			'safe_cast' => array(
				'testMarker'     => '/* testSafeCast */',
				'expectedResult' => true,
			),
			'sanitizing_function' => array(
				'testMarker'     => '/* testSanitizingFunction */',
				'expectedResult' => true,
			),
			'sanitizing_and_unslashing_function' => array(
				'testMarker'     => '/* testSanitizingAndUnslashingFunction */',
				'expectedResult' => true,
			),
			'unslashed_then_sanitized' => array(
				'testMarker'     => '/* testUnslashedThenSanitized */',
				'expectedResult' => true,
			),
			'array_walking_sanitizing_callback' => array(
				'testMarker'     => '/* testArrayWalkingSanitizingCallback */',
				'expectedResult' => true,
			),
			'string_token_sanitized' => array(
				'testMarker'     => '/* testStringTokenSanitized */',
				'expectedResult' => true,
				'tokenType'      => \T_STRING,
			),
			'fully_qualified_global_unslash_sanitized' => array(
				'testMarker'     => '/* testFullyQualifiedGlobalUnslashSanitized */',
				'expectedResult' => true,
			),

			// Namespaced inner unslash calls: true in PHPCS 3.x, false in 4.x. See the test case file and #2665.
			'partially_qualified_unslash_sanitized' => array(
				'testMarker'     => '/* testPartiallyQualifiedUnslashSanitized */',
				'expectedResult' => ( true === $is_phpcs_4 ) ? false : true,
			),
			'fully_qualified_namespaced_unslash_sanitized' => array(
				'testMarker'     => '/* testFullyQualifiedNamespacedUnslashSanitized */',
				'expectedResult' => ( true === $is_phpcs_4 ) ? false : true,
			),
			'namespace_relative_unslash_sanitized' => array(
				'testMarker'     => '/* testNamespaceRelativeUnslashSanitized */',
				'expectedResult' => ( true === $is_phpcs_4 ) ? false : true,
			),
			'namespace_relative_sub_unslash_sanitized' => array(
				'testMarker'     => '/* testNamespaceRelativeSubUnslashSanitized */',
				'expectedResult' => ( true === $is_phpcs_4 ) ? false : true,
			),
		);
	}

	/**
	 * Test that is_sanitized() invokes the unslash callback when the value is used
	 * without being unslashed, and not when the value has already been unslashed.
	 *
	 * @dataProvider dataIsSanitizedUnslashCallback
	 *
	 * @param string $testMarker     The comment which prefaces the target token in the test file.
	 * @param bool   $expectedResult The expected return value of is_sanitized().
	 * @param bool   $expectedCalled Whether the unslash callback is expected to be called.
	 *
	 * @return void
	 */
	public function testIsSanitizedUnslashCallback( $testMarker, $expectedResult, $expectedCalled ) {
		$stackPtr  = $this->getTargetToken( $testMarker, \T_VARIABLE );
		$callCount = 0;
		$callArgs  = array();
		$callback  = static function ( $phpcsFile, $ptr ) use ( &$callCount, &$callArgs ) {
			++$callCount;
			$callArgs = array( $phpcsFile, $ptr );
		};

		$result = self::$testClass->is_sanitized( self::$phpcsFile, $stackPtr, $callback );

		$this->assertSame( $expectedResult, $result, "Return value mismatch for $testMarker" );
		$this->assertSame(
			$expectedCalled ? 1 : 0,
			$callCount,
			"Unexpected number of unslash callback invocations for $testMarker"
		);

		if ( true === $expectedCalled ) {
			$this->assertSame(
				array( self::$phpcsFile, $stackPtr ),
				$callArgs,
				"The unslash callback received unexpected arguments for $testMarker"
			);
		}
	}

	/**
	 * Data provider.
	 *
	 * @see testIsSanitizedUnslashCallback()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsSanitizedUnslashCallback() {
		return array(
			'not_within_function_call' => array(
				'testMarker'     => '/* testNotWithinFunctionCall */',
				'expectedResult' => false,
				'expectedCalled' => true,
			),
			'non_sanitizing_function' => array(
				'testMarker'     => '/* testNonSanitizingFunction */',
				'expectedResult' => false,
				'expectedCalled' => true,
			),
			'sanitized_not_unslashed' => array(
				'testMarker'     => '/* testSanitizingFunction */',
				'expectedResult' => true,
				'expectedCalled' => true,
			),
			'unslashed_then_sanitized' => array(
				'testMarker'     => '/* testUnslashedThenSanitized */',
				'expectedResult' => true,
				'expectedCalled' => false,
			),
			'unslashed_in_non_sanitizing_function' => array(
				'testMarker'     => '/* testUnslashedInNonSanitizingFunction */',
				'expectedResult' => false,
				'expectedCalled' => false,
			),
			'sanitizing_and_unslashing_function' => array(
				'testMarker'     => '/* testSanitizingAndUnslashingFunction */',
				'expectedResult' => true,
				'expectedCalled' => false,
			),
		);
	}
}
