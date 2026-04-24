<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\ContextHelper;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use WordPressCS\WordPress\Helpers\ContextHelper;

/**
 * Tests for the `ContextHelper::is_in_type_test()` utility method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\ContextHelper::is_in_type_test
 */
final class IsInTypeTestUnitTest extends UtilityMethodTestCase {

	/**
	 * Test is_in_type_test().
	 *
	 * @dataProvider dataIsInTypeTest
	 *
	 * @param string $testMarker     The comment which prefaces the target token in the test file.
	 * @param bool   $expectedResult The expected return value.
	 *
	 * @return void
	 */
	public function testIsInTypeTest( $testMarker, $expectedResult ) {
		$stackPtr = $this->getTargetToken( $testMarker, \T_VARIABLE );
		$result   = ContextHelper::is_in_type_test( self::$phpcsFile, $stackPtr );

		$this->assertSame( $expectedResult, $result );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsInTypeTest()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsInTypeTest() {
		return array(
			// Cases that should return false.
			'bare_variable' => array(
				'testMarker'     => '/* testBareVariable */',
				'expectedResult' => false,
			),
			'other_function_call' => array(
				'testMarker'     => '/* testOtherFunctionCall */',
				'expectedResult' => false,
			),
			'partially_qualified_function' => array(
				'testMarker'     => '/* testPartiallyQualifiedFunction */',
				'expectedResult' => false,
			),
			'fully_qualified_namespaced_function' => array(
				'testMarker'     => '/* testFullyQualifiedNamespacedFunction */',
				'expectedResult' => false,
			),
			'namespace_relative_function' => array(
				'testMarker'     => '/* testNamespaceRelativeFunction */',
				'expectedResult' => false,
			),
			'namespace_relative_sub_function' => array(
				'testMarker'     => '/* testNamespaceRelativeSubFunction */',
				'expectedResult' => false,
			),
			'object_method' => array(
				'testMarker'     => '/* testObjectMethod */',
				'expectedResult' => false,
			),
			'nullsafe_object_method' => array(
				'testMarker'     => '/* testNullsafeObjectMethod */',
				'expectedResult' => false,
			),
			'static_method' => array(
				'testMarker'     => '/* testStaticMethod */',
				'expectedResult' => false,
			),
			'nested_non_target_function_call' => array(
				'testMarker'     => '/* testNestedNonTargetFunctionCall */',
				'expectedResult' => false,
			),

			// Cases that should return true.
			'unqualified_function' => array(
				'testMarker'     => '/* testUnqualifiedFunction */',
				'expectedResult' => true,
			),
			'mixed_case_function' => array(
				'testMarker'     => '/* testMixedCaseFunction */',
				'expectedResult' => true,
			),
			'fully_qualified_function' => array(
				'testMarker'     => '/* testFullyQualifiedFunction */',
				'expectedResult' => true,
			),
			'fully_qualified_upper_case_function' => array(
				'testMarker'     => '/* testFullyQualifiedUpperCaseFunction */',
				'expectedResult' => true,
			),
			'nested_type_test_call' => array(
				'testMarker'     => '/* testNestedTypeTestCall */',
				'expectedResult' => true,
			),
		);
	}
}
