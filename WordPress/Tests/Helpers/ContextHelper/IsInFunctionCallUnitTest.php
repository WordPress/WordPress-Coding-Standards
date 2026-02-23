<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\ContextHelper;

use WordPressCS\WordPress\Helpers\ContextHelper;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the `ContextHelper::is_in_function_call()` utility method.
 *
 * @since 3.3.0
 *
 * @covers \WordPressCS\WordPress\Helpers\ContextHelper::is_in_function_call()
 */
final class IsInFunctionCallUnitTest extends UtilityMethodTestCase {

	/**
	 * Expected results: when a test case uses this constant, `is_in_function_call()` should return `false` regardless
	 * of the value of the parameters `$global_function` and `$allow_nested`.
	 *
	 * @var array<string, bool>
	 */
	private const EXPECT_NO_MATCH = array(
		'global_only'       => false,
		'global_nested'     => false,
		'non_global_only'   => false,
		'non_global_nested' => false,
	);

	/**
	 * Expected results: when a test case uses this constant, `is_in_function_call()` should return the function name
	 * pointer regardless of the value of the parameters `$global_function` and `$allow_nested`.
	 *
	 * @var array<string, bool>
	 */
	private const EXPECT_ALWAYS_MATCH = array(
		'global_only'       => true,
		'global_nested'     => true,
		'non_global_only'   => true,
		'non_global_nested' => true,
	);

	/**
	 * Expected results: when a test case uses this constant, `is_in_function_call()` should return the function name
	 * pointer when `$global_function` is `false`, and `false` when `$global_function` is `true`.
	 *
	 * @var array<string, bool>
	 */
	private const EXPECT_NON_GLOBAL_ONLY = array(
		'global_only'       => false,
		'global_nested'     => false,
		'non_global_only'   => true,
		'non_global_nested' => true,
	);

	/**
	 * Expected results: when a test case uses this constant, `is_in_function_call()` should return the function name
	 * pointer when `$allow_nested` is `true`, and `false` when `$allow_nested` is `false`.
	 *
	 * @var array<string, bool>
	 */
	private const EXPECT_NESTED_ONLY = array(
		'global_only'       => false,
		'global_nested'     => true,
		'non_global_only'   => false,
		'non_global_nested' => true,
	);

	/**
	 * Expected results: when a test case uses this constant, `is_in_function_call()` should return the function name
	 * pointer when both `$global_function` is `false` and `$allow_nested` is `true`, and `false` otherwise.
	 *
	 * @var array<string, bool>
	 */
	private const EXPECT_NON_GLOBAL_NESTED_ONLY = array(
		'global_only'       => false,
		'global_nested'     => false,
		'non_global_only'   => false,
		'non_global_nested' => true,
	);

	/**
	 * Maps expected result keys to their corresponding is_in_function_call() parameter values.
	 *
	 * @var array<string, array<string, bool>>
	 */
	private const PARAMETER_MAP = array(
		'global_only'       => array(
			'global_function' => true,
			'allow_nested'    => false,
		),
		'global_nested'     => array(
			'global_function' => true,
			'allow_nested'    => true,
		),
		'non_global_only'   => array(
			'global_function' => false,
			'allow_nested'    => false,
		),
		'non_global_nested' => array(
			'global_function' => false,
			'allow_nested'    => true,
		),
	);

	/**
	 * Test is_in_function_call() with $global_function=true (default) and $allow_nested=false (default).
	 *
	 * @dataProvider dataIsInFunctionCall
	 *
	 * @param string              $marker         The comment which prefaces the target token in the test file.
	 * @param int|string          $tokenType      The token type to search for.
	 * @param array<string, bool> $expected       Which parameter combinations should match.
	 * @param string|null         $expectedMarker Optional. The comment for the expected function (if match expected).
	 *
	 * @return void
	 */
	public function testIsInFunctionCallWithDefaultParams( $marker, $tokenType, $expected, $expectedMarker = null ) {
		$this->runIsInFunctionCallTest(
			$marker,
			$tokenType,
			$expected['global_only'],
			$expectedMarker,
			self::PARAMETER_MAP['global_only']
		);
	}

	/**
	 * Test is_in_function_call() with $global_function=false and $allow_nested=false (default).
	 *
	 * @dataProvider dataIsInFunctionCall
	 *
	 * @param string              $marker         The comment which prefaces the target token in the test file.
	 * @param int|string          $tokenType      The token type to search for.
	 * @param array<string, bool> $expected       Which parameter combinations should match.
	 * @param string|null         $expectedMarker Optional. The comment for the expected function (if match expected).
	 *
	 * @return void
	 */
	public function testIsInFunctionCallWithGlobalFalse( $marker, $tokenType, $expected, $expectedMarker = null ) {
		$this->runIsInFunctionCallTest(
			$marker,
			$tokenType,
			$expected['non_global_only'],
			$expectedMarker,
			self::PARAMETER_MAP['non_global_only']
		);
	}

	/**
	 * Test is_in_function_call() with $global_function=true (default) and $allow_nested=true.
	 *
	 * @dataProvider dataIsInFunctionCall
	 *
	 * @param string              $marker         The comment which prefaces the target token in the test file.
	 * @param int|string          $tokenType      The token type to search for.
	 * @param array<string, bool> $expected       Which parameter combinations should match.
	 * @param string|null         $expectedMarker Optional. The comment for the expected function (if match expected).
	 *
	 * @return void
	 */
	public function testIsInFunctionCallWithNestedTrue( $marker, $tokenType, $expected, $expectedMarker = null ) {
		$this->runIsInFunctionCallTest(
			$marker,
			$tokenType,
			$expected['global_nested'],
			$expectedMarker,
			self::PARAMETER_MAP['global_nested']
		);
	}

	/**
	 * Test is_in_function_call() with $global_function=false and $allow_nested=true.
	 *
	 * @dataProvider dataIsInFunctionCall
	 *
	 * @param string              $marker         The comment which prefaces the target token in the test file.
	 * @param int|string          $tokenType      The token type to search for.
	 * @param array<string, bool> $expected       Which parameter combinations should match.
	 * @param string|null         $expectedMarker Optional. The comment for the expected function (if match expected).
	 *
	 * @return void
	 */
	public function testIsInFunctionCallWithGlobalFalseNestedTrue( $marker, $tokenType, $expected, $expectedMarker = null ) {
		$this->runIsInFunctionCallTest(
			$marker,
			$tokenType,
			$expected['non_global_nested'],
			$expectedMarker,
			self::PARAMETER_MAP['non_global_nested']
		);
	}

	/**
	 * Test is_in_function_call() when $valid_functions is an empty array.
	 *
	 * @return void
	 */
	public function testIsInFunctionCallShouldReturnFalseWhenEmptyValidFunctions() {
		$insideFunctionPtr = $this->getTargetToken( '/* testLowercaseNameInsideCall */', \T_WHITESPACE );
		$result            = ContextHelper::is_in_function_call(
			self::$phpcsFile,
			$insideFunctionPtr,
			array()
		);

		$this->assertFalse( $result );
	}

	/**
	 * Helper method to test is_in_function_call() with specific parameters.
	 *
	 * @param string              $marker         The comment which prefaces the target token.
	 * @param int|string          $tokenType      The token type to search for.
	 * @param bool                $shouldMatch    Whether the test case should match.
	 * @param string|null         $expectedMarker The comment for the expected function (if match expected).
	 * @param array<string, bool> $params         The is_in_function_call() parameter values.
	 *
	 * @return void
	 */
	private function runIsInFunctionCallTest( $marker, $tokenType, $shouldMatch, $expectedMarker, $params ) {
		$insideFunctionPtr = $this->getTargetToken( $marker, $tokenType );
		$result            = ContextHelper::is_in_function_call(
			self::$phpcsFile,
			$insideFunctionPtr,
			array(
				'valid_function1' => true,
				'valid_function2' => true,
			),
			$params['global_function'],
			$params['allow_nested']
		);

		$expected = $shouldMatch
			? $this->getTargetToken( $expectedMarker, \T_STRING )
			: false;

		$this->assertSame( $expected, $result );
	}

	/**
	 * Data provider for all is_in_function_call() tests.
	 *
	 * @return array<string, array<string, int|string|array<string, bool>>>
	 */
	public static function dataIsInFunctionCall() {
		return array(
			// Cases that should never match (regardless of parameters).
			'plain_assignment' => array(
				'marker'    => '/* testPlainAssignment */',
				'tokenType' => \T_CONSTANT_ENCAPSED_STRING,
				'expected'  => self::EXPECT_NO_MATCH,
			),
			'different_function' => array(
				'marker'    => '/* testDifferentFunction */',
				'tokenType' => \T_LNUMBER,
				'expected'  => self::EXPECT_NO_MATCH,
			),
			'inside_closure' => array(
				'marker'    => '/* testInsideClosure */',
				'tokenType' => \T_VARIABLE,
				'expected'  => self::EXPECT_NO_MATCH,
			),
			'variable_function' => array(
				'marker'    => '/* testVariableFunction */',
				'tokenType' => \T_VARIABLE,
				'expected'  => self::EXPECT_NO_MATCH,
			),
			'if_condition' => array(
				'marker'    => '/* testIfCondition */',
				'tokenType' => \T_TRUE,
				'expected'  => self::EXPECT_NO_MATCH,
			),

			// Cases that should always match (regardless of parameters).
			'lowercase_name' => array(
				'marker'         => '/* testLowercaseNameInsideCall */',
				'tokenType'      => \T_WHITESPACE,
				'expected'       => self::EXPECT_ALWAYS_MATCH,
				'expectedMarker' => '/* testLowercaseName */',
			),
			'uppercase_name' => array(
				'marker'         => '/* testUppercaseNameInsideCall */',
				'tokenType'      => \T_CONSTANT_ENCAPSED_STRING,
				'expected'       => self::EXPECT_ALWAYS_MATCH,
				'expectedMarker' => '/* testUppercaseName */',
			),
			'fully_qualified' => array(
				'marker'         => '/* testFullyQualifiedInsideCall */',
				'tokenType'      => \T_LNUMBER,
				'expected'       => self::EXPECT_ALWAYS_MATCH,
				'expectedMarker' => '/* testFullyQualified */',
			),

			// Cases that match only when `$global_function` is `false`.
			'namespaced_function' => array(
				'marker'         => '/* testNamespacedFunctionInsideCall */',
				'tokenType'      => \T_STRING,
				'expected'       => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedMarker' => '/* testNamespacedFunction */',
			),
			'fully_qualified_namespaced_function' => array(
				'marker'         => '/* testFullyQualifiedNamespacedFunctionInsideCall */',
				'tokenType'      => \T_NULL,
				'expected'       => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedMarker' => '/* testFullyQualifiedNamespacedFunction */',
			),
			'namespace_relative_function' => array(
				'marker'         => '/* testNamespaceRelativeFunctionInsideCall */',
				'tokenType'      => \T_DNUMBER,
				'expected'       => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedMarker' => '/* testNamespaceRelativeFunction */',
			),
			'static_method' => array(
				'marker'         => '/* testStaticMethodInsideCall */',
				'tokenType'      => \T_CONSTANT_ENCAPSED_STRING,
				'expected'       => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedMarker' => '/* testStaticMethod */',
			),
			'object_method' => array(
				'marker'         => '/* testObjectMethodInsideCall */',
				'tokenType'      => \T_ARRAY,
				'expected'       => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedMarker' => '/* testObjectMethod */',
			),
			'nullsafe_object_method' => array(
				'marker'         => '/* testNullsafeObjectMethodInsideCall */',
				'tokenType'      => \T_OPEN_SHORT_ARRAY,
				'expected'       => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedMarker' => '/* testNullsafeObjectMethod */',
			),

			// Cases that match only when `$allow_nested` is `true`.
			'nested_outer' => array(
				'marker'         => '/* testNestedOuterInsideCall */',
				'tokenType'      => \T_CONSTANT_ENCAPSED_STRING,
				'expected'       => self::EXPECT_NESTED_ONLY,
				'expectedMarker' => '/* testNestedOuter */',
			),
			'nested_inner' => array(
				'marker'         => '/* testNestedInnerInsideCall */',
				'tokenType'      => \T_TRUE,
				'expected'       => self::EXPECT_ALWAYS_MATCH,
				'expectedMarker' => '/* testNestedInner */',
			),
			'nested_multiple_levels' => array(
				'marker'         => '/* testNestedMultipleLevelsInsideCall */',
				'tokenType'      => \T_LNUMBER,
				'expected'       => self::EXPECT_NESTED_ONLY,
				'expectedMarker' => '/* testNestedMultipleLevels */',
			),
			'nested_both_namespaced_outer' => array(
				'marker'         => '/* testNestedBothNamespacedOuterInsideCall */',
				'tokenType'      => \T_STRING_CONCAT,
				'expected'       => self::EXPECT_NON_GLOBAL_NESTED_ONLY,
				'expectedMarker' => '/* testNestedBothNamespacedOuter */',
			),
			'complex_parameters_always_match' => array(
				'marker'         => '/* testComplexParametersAlwaysMatchInsideCall */',
				'tokenType'      => \T_VARIABLE,
				'expected'       => self::EXPECT_ALWAYS_MATCH,
				'expectedMarker' => '/* testComplexParametersAlwaysMatch */',
			),
			'complex_parameters_nested_only' => array(
				'marker'         => '/* testComplexParametersNestedOnlyInsideCall */',
				'tokenType'      => \T_VARIABLE,
				'expected'       => self::EXPECT_NESTED_ONLY,
				'expectedMarker' => '/* testComplexParametersNestedOnly */',
			),
			'complex_parameters_non_global' => array(
				'marker'         => '/* testComplexParametersNonGlobalInsideCall */',
				'tokenType'      => \T_TRUE,
				'expected'       => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedMarker' => '/* testComplexParametersNonGlobal */',
			),
			'complex_parameters_non_global_nested' => array(
				'marker'         => '/* testComplexParametersNonGlobalNestedInsideCall */',
				'tokenType'      => \T_LNUMBER,
				'expected'       => self::EXPECT_NON_GLOBAL_NESTED_ONLY,
				'expectedMarker' => '/* testComplexParametersNonGlobalNested */',
			),
		);
	}
}
