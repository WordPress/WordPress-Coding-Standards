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
	 * Identifier for the `$global_function=true, $allow_nested=false` parameter combination.
	 *
	 * @var string
	 */
	private const GLOBAL_ONLY = 'global_only';

	/**
	 * Identifier for the `$global_function=true, $allow_nested=true` parameter combination.
	 *
	 * @var string
	 */
	private const GLOBAL_NESTED = 'global_nested';

	/**
	 * Identifier for the `$global_function=false, $allow_nested=false` parameter combination.
	 *
	 * @var string
	 */
	private const NON_GLOBAL_ONLY = 'non_global_only';

	/**
	 * Identifier for the `$global_function=false, $allow_nested=true` parameter combination.
	 *
	 * @var string
	 */
	private const NON_GLOBAL_NESTED = 'non_global_nested';

	/**
	 * Expected results: when a test case uses this constant, `is_in_function_call()` should return `false` regardless
	 * of the value of the parameters `$global_function` and `$allow_nested`.
	 *
	 * @var array<string, bool>
	 */
	private const EXPECT_NO_MATCH = array(
		self::GLOBAL_ONLY       => false,
		self::GLOBAL_NESTED     => false,
		self::NON_GLOBAL_ONLY   => false,
		self::NON_GLOBAL_NESTED => false,
	);

	/**
	 * Expected results: when a test case uses this constant, `is_in_function_call()` should return the function name
	 * pointer regardless of the value of the parameters `$global_function` and `$allow_nested`.
	 *
	 * @var array<string, bool>
	 */
	private const EXPECT_ALWAYS_MATCH = array(
		self::GLOBAL_ONLY       => true,
		self::GLOBAL_NESTED     => true,
		self::NON_GLOBAL_ONLY   => true,
		self::NON_GLOBAL_NESTED => true,
	);

	/**
	 * Expected results: when a test case uses this constant, `is_in_function_call()` should return the function name
	 * pointer when `$global_function` is `false`, and `false` when `$global_function` is `true`.
	 *
	 * @var array<string, bool>
	 */
	private const EXPECT_NON_GLOBAL_ONLY = array(
		self::GLOBAL_ONLY       => false,
		self::GLOBAL_NESTED     => false,
		self::NON_GLOBAL_ONLY   => true,
		self::NON_GLOBAL_NESTED => true,
	);

	/**
	 * Expected results: when a test case uses this constant, `is_in_function_call()` should return the function name
	 * pointer when `$allow_nested` is `true`, and `false` when `$allow_nested` is `false`.
	 *
	 * @var array<string, bool>
	 */
	private const EXPECT_NESTED_ONLY = array(
		self::GLOBAL_ONLY       => false,
		self::GLOBAL_NESTED     => true,
		self::NON_GLOBAL_ONLY   => false,
		self::NON_GLOBAL_NESTED => true,
	);

	/**
	 * Expected results: when a test case uses this constant, `is_in_function_call()` should return the function name
	 * pointer when both `$global_function` is `false` and `$allow_nested` is `true`, and `false` otherwise.
	 *
	 * @var array<string, bool>
	 */
	private const EXPECT_NON_GLOBAL_NESTED_ONLY = array(
		self::GLOBAL_ONLY       => false,
		self::GLOBAL_NESTED     => false,
		self::NON_GLOBAL_ONLY   => false,
		self::NON_GLOBAL_NESTED => true,
	);

	/**
	 * Maps expected result keys to their corresponding is_in_function_call() parameter values.
	 *
	 * @var array<string, array<string, bool>>
	 */
	private const PARAMETER_MAP = array(
		self::GLOBAL_ONLY       => array(
			'global_function' => true,
			'allow_nested'    => false,
		),
		self::GLOBAL_NESTED     => array(
			'global_function' => true,
			'allow_nested'    => true,
		),
		self::NON_GLOBAL_ONLY   => array(
			'global_function' => false,
			'allow_nested'    => false,
		),
		self::NON_GLOBAL_NESTED => array(
			'global_function' => false,
			'allow_nested'    => true,
		),
	);

	/**
	 * Test is_in_function_call() when $valid_functions is an empty array.
	 *
	 * @return void
	 */
	public function testIsInFunctionCallShouldReturnFalseWhenEmptyValidFunctions() {
		$insideFunctionPtr = $this->getTargetToken( '/* testLowercaseNameInsideCall */', \T_VARIABLE );
		$result            = ContextHelper::is_in_function_call(
			self::$phpcsFile,
			$insideFunctionPtr,
			array()
		);

		$this->assertFalse( $result );
	}

	/**
	 * Test to document that is_in_function_call() does not match when $valid_functions keys are not lowercase.
	 *
	 * @return void
	 */
	public function testIsInFunctionCallShouldReturnFalseWhenValidFunctionsKeysAreNotLowercase() {
		$insideFunctionPtr = $this->getTargetToken( '/* testLowercaseNameInsideCall */', \T_VARIABLE );
		$result            = ContextHelper::is_in_function_call(
			self::$phpcsFile,
			$insideFunctionPtr,
			array(
				'Valid_Function1' => true,
			)
		);

		$this->assertFalse( $result );
	}

	/**
	 * Test is_in_function_call() with $global_function=true (default) and $allow_nested=false (default).
	 *
	 * @dataProvider dataIsInFunctionCallWithDefaultParams
	 *
	 * @param string              $marker         The comment which prefaces the target token in the test file.
	 * @param int|string          $tokenType      The token type to search for.
	 * @param bool                $shouldMatch    Whether `is_in_function_call()` should find a match.
	 * @param string|null         $expectedMarker The comment which prefaces the expected function name
	 *                                            in the test file (if a match is expected).
	 * @param array<string, bool> $params         The is_in_function_call() parameter values.
	 *
	 * @return void
	 */
	public function testIsInFunctionCallWithDefaultParams( $marker, $tokenType, $shouldMatch, $expectedMarker, $params ) {
		$this->runIsInFunctionCallTest( $marker, $tokenType, $shouldMatch, $expectedMarker, $params );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsInFunctionCallWithDefaultParams()
	 *
	 * @return array<string, array<string, int|string|bool|null|array<string, bool>>>
	 */
	public static function dataIsInFunctionCallWithDefaultParams() {
		$data = self::dataIsInFunctionCall();

		// Update 'shouldMatch' and 'params' to only contain the values relevant for this test.
		foreach ( $data as $key => $dataset ) {
			$data[ $key ]['shouldMatch'] = $dataset['shouldMatch'][ self::GLOBAL_ONLY ];
			$data[ $key ]['params']      = self::PARAMETER_MAP[ self::GLOBAL_ONLY ];
		}

		return $data;
	}

	/**
	 * Test is_in_function_call() with $global_function=false and $allow_nested=false (default).
	 *
	 * @dataProvider dataIsInFunctionCallWithGlobalFalse
	 *
	 * @param string              $marker         The comment which prefaces the target token in the test file.
	 * @param int|string          $tokenType      The token type to search for.
	 * @param bool                $shouldMatch    Whether `is_in_function_call()` should find a match.
	 * @param string|null         $expectedMarker The comment which prefaces the expected function name
	 *                                            in the test file (if a match is expected).
	 * @param array<string, bool> $params         The is_in_function_call() parameter values.
	 *
	 * @return void
	 */
	public function testIsInFunctionCallWithGlobalFalse( $marker, $tokenType, $shouldMatch, $expectedMarker, $params ) {
		$this->runIsInFunctionCallTest( $marker, $tokenType, $shouldMatch, $expectedMarker, $params );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsInFunctionCallWithGlobalFalse()
	 *
	 * @return array<string, array<string, int|string|bool|null|array<string, bool>>>
	 */
	public static function dataIsInFunctionCallWithGlobalFalse() {
		$data = self::dataIsInFunctionCall();

		// Update 'shouldMatch' and 'params' to only contain the values relevant for this test.
		foreach ( $data as $key => $dataset ) {
			$data[ $key ]['shouldMatch'] = $dataset['shouldMatch'][ self::NON_GLOBAL_ONLY ];
			$data[ $key ]['params']      = self::PARAMETER_MAP[ self::NON_GLOBAL_ONLY ];
		}

		return $data;
	}

	/**
	 * Test is_in_function_call() with $global_function=true (default) and $allow_nested=true.
	 *
	 * @dataProvider dataIsInFunctionCallWithNestedTrue
	 *
	 * @param string              $marker         The comment which prefaces the target token in the test file.
	 * @param int|string          $tokenType      The token type to search for.
	 * @param bool                $shouldMatch    Whether `is_in_function_call()` should find a match.
	 * @param string|null         $expectedMarker The comment which prefaces the expected function name
	 *                                            in the test file (if a match is expected).
	 * @param array<string, bool> $params         The is_in_function_call() parameter values.
	 *
	 * @return void
	 */
	public function testIsInFunctionCallWithNestedTrue( $marker, $tokenType, $shouldMatch, $expectedMarker, $params ) {
		$this->runIsInFunctionCallTest( $marker, $tokenType, $shouldMatch, $expectedMarker, $params );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsInFunctionCallWithNestedTrue()
	 *
	 * @return array<string, array<string, int|string|bool|null|array<string, bool>>>
	 */
	public static function dataIsInFunctionCallWithNestedTrue() {
		$data = self::dataIsInFunctionCall();

		// Update 'shouldMatch' and 'params' to only contain the values relevant for this test.
		foreach ( $data as $key => $dataset ) {
			$data[ $key ]['shouldMatch'] = $dataset['shouldMatch'][ self::GLOBAL_NESTED ];
			$data[ $key ]['params']      = self::PARAMETER_MAP[ self::GLOBAL_NESTED ];
		}

		return $data;
	}

	/**
	 * Test is_in_function_call() with $global_function=false and $allow_nested=true.
	 *
	 * @dataProvider dataIsInFunctionCallWithGlobalFalseNestedTrue
	 *
	 * @param string              $marker         The comment which prefaces the target token in the test file.
	 * @param int|string          $tokenType      The token type to search for.
	 * @param bool                $shouldMatch    Whether `is_in_function_call()` should find a match.
	 * @param string|null         $expectedMarker The comment which prefaces the expected function name
	 *                                            in the test file (if a match is expected).
	 * @param array<string, bool> $params         The is_in_function_call() parameter values.
	 *
	 * @return void
	 */
	public function testIsInFunctionCallWithGlobalFalseNestedTrue( $marker, $tokenType, $shouldMatch, $expectedMarker, $params ) {
		$this->runIsInFunctionCallTest( $marker, $tokenType, $shouldMatch, $expectedMarker, $params );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsInFunctionCallWithGlobalFalseNestedTrue()
	 *
	 * @return array<string, array<string, int|string|bool|null|array<string, bool>>>
	 */
	public static function dataIsInFunctionCallWithGlobalFalseNestedTrue() {
		$data = self::dataIsInFunctionCall();

		// Update 'shouldMatch' and 'params' to only contain the values relevant for this test.
		foreach ( $data as $key => $dataset ) {
			$data[ $key ]['shouldMatch'] = $dataset['shouldMatch'][ self::NON_GLOBAL_NESTED ];
			$data[ $key ]['params']      = self::PARAMETER_MAP[ self::NON_GLOBAL_NESTED ];
		}

		return $data;
	}

	/**
	 * Helper method to test is_in_function_call() with specific parameters.
	 *
	 * @param string              $marker         The comment which prefaces the target token.
	 * @param int|string          $tokenType      The token type to search for.
	 * @param bool                $shouldMatch    Whether `is_in_function_call()` should find a match.
	 * @param string|null         $expectedMarker The comment which prefaces the expected function name
	 *                                            in the test file (if a match is expected).
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

		$expected = false;
		if ( true === $shouldMatch ) {
			$expected = $this->getTargetToken( $expectedMarker, \T_STRING );
		}

		$this->assertSame( $expected, $result );
	}

	/**
	 * Base data provider. Wrapper data providers adapt this data for their specific parameter combination.
	 *
	 * @return array<string, array<string, int|string|array<string, bool>>>
	 */
	public static function dataIsInFunctionCall() {
		$data = array(
			// Cases that should never match (regardless of parameters).
			'plain_assignment' => array(
				'marker'      => '/* testPlainAssignment */',
				'tokenType'   => \T_CONSTANT_ENCAPSED_STRING,
				'shouldMatch' => self::EXPECT_NO_MATCH,
			),
			'different_function' => array(
				'marker'      => '/* testDifferentFunction */',
				'tokenType'   => \T_LNUMBER,
				'shouldMatch' => self::EXPECT_NO_MATCH,
			),
			'inside_closure' => array(
				'marker'      => '/* testInsideClosure */',
				'tokenType'   => \T_VARIABLE,
				'shouldMatch' => self::EXPECT_NO_MATCH,
			),
			'variable_function' => array(
				'marker'      => '/* testVariableFunction */',
				'tokenType'   => \T_VARIABLE,
				'shouldMatch' => self::EXPECT_NO_MATCH,
			),
			'if_condition' => array(
				'marker'      => '/* testIfCondition */',
				'tokenType'   => \T_TRUE,
				'shouldMatch' => self::EXPECT_NO_MATCH,
			),

			// Cases that should always match (regardless of parameters).
			'lowercase_name' => array(
				'marker'         => '/* testLowercaseNameInsideCall */',
				'tokenType'      => \T_VARIABLE,
				'shouldMatch'    => self::EXPECT_ALWAYS_MATCH,
				'expectedMarker' => '/* testLowercaseName */',
			),
			'uppercase_name' => array(
				'marker'         => '/* testUppercaseNameInsideCall */',
				'tokenType'      => \T_CONSTANT_ENCAPSED_STRING,
				'shouldMatch'    => self::EXPECT_ALWAYS_MATCH,
				'expectedMarker' => '/* testUppercaseName */',
			),
			'fully_qualified' => array(
				'marker'         => '/* testFullyQualifiedInsideCall */',
				'tokenType'      => \T_LNUMBER,
				'shouldMatch'    => self::EXPECT_ALWAYS_MATCH,
				'expectedMarker' => '/* testFullyQualified */',
			),
			'nested_inner' => array(
				'marker'         => '/* testNestedInnerInsideCall */',
				'tokenType'      => \T_TRUE,
				'shouldMatch'    => self::EXPECT_ALWAYS_MATCH,
				'expectedMarker' => '/* testNestedInner */',
			),

			// Cases that match only when `$global_function` is `false`.
			'namespaced_function' => array(
				'marker'         => '/* testNamespacedFunctionInsideCall */',
				'tokenType'      => \T_STRING,
				'shouldMatch'    => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedMarker' => '/* testNamespacedFunction */',
			),
			'fully_qualified_namespaced_function' => array(
				'marker'         => '/* testFullyQualifiedNamespacedFunctionInsideCall */',
				'tokenType'      => \T_NULL,
				'shouldMatch'    => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedMarker' => '/* testFullyQualifiedNamespacedFunction */',
			),
			'namespace_relative_function' => array(
				'marker'         => '/* testNamespaceRelativeFunctionInsideCall */',
				'tokenType'      => \T_DNUMBER,
				'shouldMatch'    => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedMarker' => '/* testNamespaceRelativeFunction */',
			),
			'static_method' => array(
				'marker'         => '/* testStaticMethodInsideCall */',
				'tokenType'      => \T_CONSTANT_ENCAPSED_STRING,
				'shouldMatch'    => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedMarker' => '/* testStaticMethod */',
			),
			'object_method' => array(
				'marker'         => '/* testObjectMethodInsideCall */',
				'tokenType'      => \T_ARRAY,
				'shouldMatch'    => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedMarker' => '/* testObjectMethod */',
			),
			'nullsafe_object_method' => array(
				'marker'         => '/* testNullsafeObjectMethodInsideCall */',
				'tokenType'      => \T_OPEN_SHORT_ARRAY,
				'shouldMatch'    => self::EXPECT_NON_GLOBAL_ONLY,
				'expectedMarker' => '/* testNullsafeObjectMethod */',
			),

			// Cases that match only when `$allow_nested` is `true`.
			'nested_outer' => array(
				'marker'         => '/* testNestedOuterInsideCall */',
				'tokenType'      => \T_CONSTANT_ENCAPSED_STRING,
				'shouldMatch'    => self::EXPECT_NESTED_ONLY,
				'expectedMarker' => '/* testNestedOuter */',
			),
			'nested_multiple_levels' => array(
				'marker'         => '/* testNestedMultipleLevelsInsideCall */',
				'tokenType'      => \T_LNUMBER,
				'shouldMatch'    => self::EXPECT_NESTED_ONLY,
				'expectedMarker' => '/* testNestedMultipleLevels */',
			),
			'nested_both_namespaced_outer' => array(
				'marker'         => '/* testNestedBothNamespacedOuterInsideCall */',
				'tokenType'      => \T_STRING_CONCAT,
				'shouldMatch'    => self::EXPECT_NON_GLOBAL_NESTED_ONLY,
				'expectedMarker' => '/* testNestedBothNamespacedOuter */',
			),

			// Safeguard: parentheses in other parameters should not confuse the method.
			'other_params_with_parentheses' => array(
				'marker'         => '/* testOtherParamsWithParenthesesInsideCall */',
				'tokenType'      => \T_VARIABLE,
				'shouldMatch'    => self::EXPECT_ALWAYS_MATCH,
				'expectedMarker' => '/* testOtherParamsWithParentheses */',
			),
		);

		foreach ( $data as $key => $dataset ) {
			if ( isset( $dataset['expectedMarker'] ) === false ) {
				$data[ $key ]['expectedMarker'] = null;
			}
		}

		return $data;
	}
}
