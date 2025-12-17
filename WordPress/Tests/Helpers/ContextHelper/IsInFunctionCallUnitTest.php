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
	const EXPECT_NO_MATCH = array(
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
	const EXPECT_ALWAYS_MATCH = array(
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
	const EXPECT_NON_GLOBAL_ONLY = array(
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
	const EXPECT_NESTED_ONLY = array(
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
	const EXPECT_NON_GLOBAL_NESTED_ONLY = array(
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
	const PARAMETER_MAP = array(
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
	 * @param string              $testMarker             The comment which prefaces the target token in the test file.
	 * @param int|string          $tokenType              The token type to search for.
	 * @param array<string, bool> $expectedResults        Which parameter combinations should match.
	 * @param string|null         $expectedFunctionMarker Optional. The comment for the expected function (if match expected).
	 *
	 * @return void
	 */
	public function testIsInFunctionCallWithDefaultParams(
		$testMarker,
		$tokenType,
		$expectedResults,
		$expectedFunctionMarker = null
	) {
		$this->runIsInFunctionCallTest(
			$testMarker,
			$tokenType,
			$expectedResults,
			$expectedFunctionMarker,
			'global_only'
		);
	}

	/**
	 * Test is_in_function_call() with $global_function=false and $allow_nested=false (default).
	 *
	 * @dataProvider dataIsInFunctionCall
	 *
	 * @param string              $testMarker             The comment which prefaces the target token in the test file.
	 * @param int|string          $tokenType              The token type to search for.
	 * @param array<string, bool> $expectedResults        Which parameter combinations should match.
	 * @param string|null         $expectedFunctionMarker Optional. The comment for the expected function (if match expected).
	 *
	 * @return void
	 */
	public function testIsInFunctionCallWithGlobalFalse(
		$testMarker,
		$tokenType,
		$expectedResults,
		$expectedFunctionMarker = null
	) {
		$this->runIsInFunctionCallTest(
			$testMarker,
			$tokenType,
			$expectedResults,
			$expectedFunctionMarker,
			'non_global_only'
		);
	}

	/**
	 * Test is_in_function_call() with $global_function=true (default) and $allow_nested=true.
	 *
	 * @dataProvider dataIsInFunctionCall
	 *
	 * @param string              $testMarker             The comment which prefaces the target token in the test file.
	 * @param int|string          $tokenType              The token type to search for.
	 * @param array<string, bool> $expectedResults        Which parameter combinations should match.
	 * @param string|null         $expectedFunctionMarker Optional. The comment for the expected function (if match expected).
	 *
	 * @return void
	 */
	public function testIsInFunctionCallWithNestedTrue(
		$testMarker,
		$tokenType,
		$expectedResults,
		$expectedFunctionMarker = null
	) {
		$this->runIsInFunctionCallTest(
			$testMarker,
			$tokenType,
			$expectedResults,
			$expectedFunctionMarker,
			'global_nested'
		);
	}

	/**
	 * Test is_in_function_call() with $global_function=false and $allow_nested=true.
	 *
	 * @dataProvider dataIsInFunctionCall
	 *
	 * @param string              $testMarker             The comment which prefaces the target token in the test file.
	 * @param int|string          $tokenType              The token type to search for.
	 * @param array<string, bool> $expectedResults        Which parameter combinations should match.
	 * @param string|null         $expectedFunctionMarker Optional. The comment for the expected function (if match expected).
	 *
	 * @return void
	 */
	public function testIsInFunctionCallWithGlobalFalseNestedTrue(
		$testMarker,
		$tokenType,
		$expectedResults,
		$expectedFunctionMarker = null
	) {
		$this->runIsInFunctionCallTest(
			$testMarker,
			$tokenType,
			$expectedResults,
			$expectedFunctionMarker,
			'non_global_nested'
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
	 * @param string              $testMarker             The comment which prefaces the target token.
	 * @param int|string          $tokenType              The token type to search for.
	 * @param array<string, bool> $expectedResults        Which parameter combinations should match.
	 * @param string|null         $expectedFunctionMarker The comment for the expected function (if match expected).
	 * @param string              $expectedKey            Which key in expectedResults to check.
	 *
	 * @return void
	 */
	private function runIsInFunctionCallTest(
		$testMarker,
		$tokenType,
		$expectedResults,
		$expectedFunctionMarker,
		$expectedKey
	) {
		$globalFunction = self::PARAMETER_MAP[ $expectedKey ]['global_function'];
		$allowNested    = self::PARAMETER_MAP[ $expectedKey ]['allow_nested'];

		$insideFunctionPtr = $this->getTargetToken( $testMarker, $tokenType );
		$result            = ContextHelper::is_in_function_call(
			self::$phpcsFile,
			$insideFunctionPtr,
			array(
				'valid_function1' => true,
				'valid_function2' => true,
			),
			$globalFunction,
			$allowNested
		);

		$expected = $expectedResults[ $expectedKey ]
			? $this->getTargetToken( $expectedFunctionMarker, \T_STRING )
			: false;

		$this->assertSame( $expected, $result, "Failed for: $testMarker with $expectedKey" );
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
				'testMarker'      => '/* testPlainAssignment */',
				'tokenType'       => \T_CONSTANT_ENCAPSED_STRING,
				'expectedResults' => self::EXPECT_NO_MATCH,
			),
			'different_function' => array(
				'testMarker'      => '/* testDifferentFunction */',
				'tokenType'       => \T_LNUMBER,
				'expectedResults' => self::EXPECT_NO_MATCH,
			),
			'inside_closure' => array(
				'testMarker'      => '/* testInsideClosure */',
				'tokenType'       => \T_VARIABLE,
				'expectedResults' => self::EXPECT_NO_MATCH,
			),
			'variable_function' => array(
				'testMarker'      => '/* testVariableFunction */',
				'tokenType'       => \T_VARIABLE,
				'expectedResults' => self::EXPECT_NO_MATCH,
			),
			'if_condition' => array(
				'testMarker'      => '/* testIfCondition */',
				'tokenType'       => \T_TRUE,
				'expectedResults' => self::EXPECT_NO_MATCH,
			),

			// Cases that should always match (regardless of parameters).
			'lowercase_name' => array(
				'testMarker'             => '/* testLowercaseNameInsideCall */',
				'tokenType'              => \T_WHITESPACE,
				'expectedResults'        => self::EXPECT_ALWAYS_MATCH,
				'expectedFunctionMarker' => '/* testLowercaseName */',
			),
			'uppercase_name' => array(
				'testMarker'             => '/* testUppercaseNameInsideCall */',
				'tokenType'              => \T_CONSTANT_ENCAPSED_STRING,
				'expectedResults'        => self::EXPECT_ALWAYS_MATCH,
				'expectedFunctionMarker' => '/* testUppercaseName */',
			),
			'fully_qualified' => array(
				'testMarker'             => '/* testFullyQualifiedInsideCall */',
				'tokenType'              => \T_LNUMBER,
				'expectedResults'        => self::EXPECT_ALWAYS_MATCH,
				'expectedFunctionMarker' => '/* testFullyQualified */',
			),

			// Cases that match only when `$global_function` is `false`.
			'namespaced_function' => array(
				'testMarker'             => '/* testNamespacedFunctionInsideCall */',
				'tokenType'              => \T_STRING,
				'expectedResults'        => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedFunctionMarker' => '/* testNamespacedFunction */',
			),
			'fully_qualified_namespaced_function' => array(
				'testMarker'             => '/* testFullyQualifiedNamespacedFunctionInsideCall */',
				'tokenType'              => \T_NULL,
				'expectedResults'        => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedFunctionMarker' => '/* testFullyQualifiedNamespacedFunction */',
			),
			'namespace_relative_function' => array(
				'testMarker'             => '/* testNamespaceRelativeFunctionInsideCall */',
				'tokenType'              => \T_DNUMBER,
				'expectedResults'        => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedFunctionMarker' => '/* testNamespaceRelativeFunction */',
			),
			'static_method' => array(
				'testMarker'             => '/* testStaticMethodInsideCall */',
				'tokenType'              => \T_CONSTANT_ENCAPSED_STRING,
				'expectedResults'        => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedFunctionMarker' => '/* testStaticMethod */',
			),
			'object_method' => array(
				'testMarker'             => '/* testObjectMethodInsideCall */',
				'tokenType'              => \T_ARRAY,
				'expectedResults'        => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedFunctionMarker' => '/* testObjectMethod */',
			),
			'nullsafe_object_method' => array(
				'testMarker'             => '/* testNullsafeObjectMethodInsideCall */',
				'tokenType'              => \T_OPEN_SHORT_ARRAY,
				'expectedResults'        => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedFunctionMarker' => '/* testNullsafeObjectMethod */',
			),

			// Cases that match only when `$allow_nested` is `true`.
			'nested_outer' => array(
				'testMarker'             => '/* testNestedOuterInsideCall */',
				'tokenType'              => \T_CONSTANT_ENCAPSED_STRING,
				'expectedResults'        => self::EXPECT_NESTED_ONLY,
				'expectedFunctionMarker' => '/* testNestedOuter */',
			),
			'nested_inner' => array(
				'testMarker'             => '/* testNestedInnerInsideCall */',
				'tokenType'              => \T_TRUE,
				'expectedResults'        => self::EXPECT_ALWAYS_MATCH,
				'expectedFunctionMarker' => '/* testNestedInner */',
			),
			'nested_multiple_levels' => array(
				'testMarker'             => '/* testNestedMultipleLevelsInsideCall */',
				'tokenType'              => \T_LNUMBER,
				'expectedResults'        => self::EXPECT_NESTED_ONLY,
				'expectedFunctionMarker' => '/* testNestedMultipleLevels */',
			),
			'nested_both_namespaced_outer' => array(
				'testMarker'             => '/* testNestedBothNamespacedOuterInsideCall */',
				'tokenType'              => \T_STRING_CONCAT,
				'expectedResults'        => self::EXPECT_NON_GLOBAL_NESTED_ONLY,
				'expectedFunctionMarker' => '/* testNestedBothNamespacedOuter */',
			),
			'complex_parameters_always_match' => array(
				'testMarker'             => '/* testComplexParametersAlwaysMatchInsideCall */',
				'tokenType'              => \T_VARIABLE,
				'expectedResults'        => self::EXPECT_ALWAYS_MATCH,
				'expectedFunctionMarker' => '/* testComplexParametersAlwaysMatch */',
			),
			'complex_parameters_nested_only' => array(
				'testMarker'             => '/* testComplexParametersNestedOnlyInsideCall */',
				'tokenType'              => \T_VARIABLE,
				'expectedResults'        => self::EXPECT_NESTED_ONLY,
				'expectedFunctionMarker' => '/* testComplexParametersNestedOnly */',
			),
			'complex_parameters_non_global' => array(
				'testMarker'             => '/* testComplexParametersNonGlobalInsideCall */',
				'tokenType'              => \T_TRUE,
				'expectedResults'        => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedFunctionMarker' => '/* testComplexParametersNonGlobal */',
			),
			'complex_parameters_non_global_nested' => array(
				'testMarker'             => '/* testComplexParametersNonGlobalNestedInsideCall */',
				'tokenType'              => \T_LNUMBER,
				'expectedResults'        => self::EXPECT_NON_GLOBAL_NESTED_ONLY,
				'expectedFunctionMarker' => '/* testComplexParametersNonGlobalNested */',
			),
		);
	}
}
