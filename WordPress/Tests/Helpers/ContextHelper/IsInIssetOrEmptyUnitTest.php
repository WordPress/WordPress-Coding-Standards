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
 * Tests for the `ContextHelper::is_in_isset_or_empty()` utility method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\ContextHelper::is_in_isset_or_empty
 */
final class IsInIssetOrEmptyUnitTest extends UtilityMethodTestCase {

	/**
	 * Test is_in_isset_or_empty().
	 *
	 * @dataProvider dataIsInIssetOrEmpty
	 *
	 * @param string $testMarker     The comment which prefaces the target token in the test file.
	 * @param bool   $expectedResult The expected return value.
	 *
	 * @return void
	 */
	public function testIsInIssetOrEmpty( $testMarker, $expectedResult ) {
		$stackPtr = $this->getTargetToken( $testMarker, \T_VARIABLE );
		$result   = ContextHelper::is_in_isset_or_empty( self::$phpcsFile, $stackPtr );

		$this->assertSame( $expectedResult, $result );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsInIssetOrEmpty()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsInIssetOrEmpty() {
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
			'missing_array_param' => array(
				'testMarker'     => '/* testMissingArrayParam */',
				'expectedResult' => false,
			),
			'key_param_not_array_param' => array(
				'testMarker'     => '/* testKeyParamNotArrayParam */',
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
			'isset_object_method' => array(
				'testMarker'     => '/* testIssetObjectMethod */',
				'expectedResult' => false,
			),
			'empty_static_method' => array(
				'testMarker'     => '/* testEmptyStaticMethod */',
				'expectedResult' => false,
			),
			'isset_namespaced_function' => array(
				'testMarker'     => '/* testIssetNamespacedFunction */',
				'expectedResult' => false,
			),

			// Cases that should return true.
			'isset' => array(
				'testMarker'     => '/* testIsset */',
				'expectedResult' => true,
			),
			'empty' => array(
				'testMarker'     => '/* testEmpty */',
				'expectedResult' => true,
			),
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
			'named_param_reversed_order' => array(
				'testMarker'     => '/* testNamedParamReversedOrder */',
				'expectedResult' => true,
			),
			'nested_valid_function_call' => array(
				'testMarker'     => '/* testNestedValidFunctionCall */',
				'expectedResult' => true,
			),
		);
	}
}
