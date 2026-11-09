<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\ValidationHelper;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use WordPressCS\WordPress\Helpers\ValidationHelper;

/**
 * Tests for the `ValidationHelper::is_validated()` utility method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\ValidationHelper::is_validated
 */
final class IsValidatedUnitTest extends UtilityMethodTestCase {

	/**
	 * Test is_validated() returns false if the token does not exist.
	 *
	 * @return void
	 */
	public function testIsValidatedReturnsFalseIfTokenDoesNotExist() {
		$this->assertFalse( ValidationHelper::is_validated( self::$phpcsFile, -1 ) );
	}

	/**
	 * Test is_validated() handles live coding / parse error situations.
	 *
	 * @dataProvider dataIsValidatedLiveCoding
	 *
	 * @param string $testCaseFile The test case file to parse.
	 *
	 * @return void
	 */
	public function testIsValidatedLiveCoding( $testCaseFile ) {
		$this->assertIsValidatedInFile( $testCaseFile, false );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsValidatedLiveCoding()
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function dataIsValidatedLiveCoding() {
		return array(
			'construct_not_followed_by_parenthesis' => array(
				'testCaseFile' => 'IsValidatedConstructNotFollowedByParenthesisUnitTest.inc',
			),
			'construct_unclosed_parenthesis' => array(
				'testCaseFile' => 'IsValidatedConstructUnclosedParenthesisUnitTest.inc',
			),
		);
	}

	/**
	 * Test is_validated() correctly respects scope boundaries between file scope and function scope.
	 *
	 * @dataProvider dataIsValidatedScopeBoundaries
	 *
	 * @param string $testCaseFile   The test case file to parse.
	 * @param bool   $expectedResult The expected return value.
	 *
	 * @return void
	 */
	public function testIsValidatedScopeBoundaries( $testCaseFile, $expectedResult ) {
		$this->assertIsValidatedInFile( $testCaseFile, $expectedResult );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsValidatedScopeBoundaries()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsValidatedScopeBoundaries() {
		return array(
			'function_validation_not_seen_at_file_scope' => array(
				'testCaseFile'   => 'IsValidatedFunctionValidationNotSeenAtFileScopeUnitTest.inc',
				'expectedResult' => false,
			),
			'outer_validation_not_seen_in_function' => array(
				'testCaseFile'   => 'IsValidatedOuterValidationNotSeenInFunctionUnitTest.inc',
				'expectedResult' => false,
			),
			'validated_at_file_scope' => array(
				'testCaseFile'   => 'IsValidatedValidatedAtFileScopeUnitTest.inc',
				'expectedResult' => true,
			),
		);
	}

	/**
	 * Test is_validated() with default parameter for $in_condition_only and a single $array_keys entry.
	 *
	 * @dataProvider dataIsValidated
	 *
	 * @param string $testMarker     The comment which prefaces the target token in the test file.
	 * @param bool   $expectedResult The expected return value.
	 *
	 * @return void
	 */
	public function testIsValidated( $testMarker, $expectedResult ) {
		$stackPtr = $this->getTargetToken( $testMarker, \T_VARIABLE );
		$result   = ValidationHelper::is_validated( self::$phpcsFile, $stackPtr, array( 'key' ) );

		$this->assertSame( $expectedResult, $result );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsValidated()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsValidated() {
		return array(
			// Cases that should return false.
			'not_validated' => array(
				'testMarker'     => '/* testNotValidated */',
				'expectedResult' => false,
			),
			'outer_function_validation_not_counted' => array(
				'testMarker'     => '/* testOuterFunctionValidationNotCounted */',
				'expectedResult' => false,
			),
			'closed_scope_validation_not_counted' => array(
				'testMarker'     => '/* testClosedScopeValidationNotCounted */',
				'expectedResult' => false,
			),
			'arrow_function_validation_not_counted' => array(
				'testMarker'     => '/* testArrowFunctionValidationNotCounted */',
				'expectedResult' => false,
			),
			'construct_wrong_variable_or_key' => array(
				'testMarker'     => '/* testConstructWrongVariableOrKey */',
				'expectedResult' => false,
			),
			'function_name_used_as_constant' => array(
				'testMarker'     => '/* testFunctionNameUsedAsConstant */',
				'expectedResult' => false,
			),
			'function_call_in_attribute' => array(
				'testMarker'     => '/* testFunctionCallInAttribute */',
				'expectedResult' => false,
			),
			'function_call_non_global' => array(
				'testMarker'     => '/* testFunctionCallNonGlobal */',
				'expectedResult' => false,
			),
			'function_call_namespaced' => array(
				'testMarker'     => '/* testFunctionCallNamespaced */',
				'expectedResult' => false,
			),
			'function_call_missing_parameters' => array(
				'testMarker'     => '/* testFunctionCallMissingParameters */',
				'expectedResult' => false,
			),
			'function_call_wrong_array_param' => array(
				'testMarker'     => '/* testFunctionCallWrongArrayParam */',
				'expectedResult' => false,
			),
			'function_call_mismatched_key' => array(
				'testMarker'     => '/* testFunctionCallMismatchedKey */',
				'expectedResult' => false,
			),
			'coalesce_no_match' => array(
				'testMarker'     => '/* testCoalesceNoMatch */',
				'expectedResult' => false,
			),

			// Cases that should return true.
			'validated_with_isset' => array(
				'testMarker'     => '/* testValidatedWithIsset */',
				'expectedResult' => true,
			),
			'validated_with_empty' => array(
				'testMarker'     => '/* testValidatedWithEmpty */',
				'expectedResult' => true,
			),
			'validated_in_closure' => array(
				'testMarker'     => '/* testValidatedInClosure */',
				'expectedResult' => true,
			),
			'function_call' => array(
				'testMarker'     => '/* testFunctionCall */',
				'expectedResult' => true,
			),
			'function_call_mixed_case' => array(
				'testMarker'     => '/* testFunctionCallMixedCase */',
				'expectedResult' => true,
			),
			'function_call_fully_qualified' => array(
				'testMarker'     => '/* testFunctionCallFullyQualified */',
				'expectedResult' => true,
			),
			'function_call_fully_qualified_uppercase' => array(
				'testMarker'     => '/* testFunctionCallFullyQualifiedUppercase */',
				'expectedResult' => true,
			),
			'validated_with_null_coalesce' => array(
				'testMarker'     => '/* testValidatedWithNullCoalesce */',
				'expectedResult' => true,
			),
			'validated_with_coalesce_equal' => array(
				'testMarker'     => '/* testValidatedWithCoalesceEqual */',
				'expectedResult' => true,
			),
		);
	}

	/**
	 * Test is_validated() with $in_condition_only set to true returns false when the variable
	 * is not inside a condition or the condition has no parentheses.
	 *
	 * @dataProvider dataIsValidatedInConditionOnlyReturnsFalse
	 *
	 * @param string $testCaseFile The test case file to parse.
	 *
	 * @return void
	 */
	public function testIsValidatedInConditionOnlyReturnsFalse( $testCaseFile ) {
		$this->assertIsValidatedInFile( $testCaseFile, false, true );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsValidatedInConditionOnlyReturnsFalse()
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function dataIsValidatedInConditionOnlyReturnsFalse() {
		return array(
			'no_condition' => array(
				'testCaseFile' => 'IsValidatedValidatedAtFileScopeUnitTest.inc',
			),
			'condition_without_parentheses' => array(
				'testCaseFile' => 'IsValidatedInConditionOnlyNoParenthesisUnitTest.inc',
			),
		);
	}

	/**
	 * Test is_validated() with $in_condition_only set to true.
	 *
	 * @dataProvider dataIsValidatedInConditionOnly
	 *
	 * @param string $testMarker     The comment which prefaces the target token in the test file.
	 * @param bool   $expectedResult The expected return value.
	 *
	 * @return void
	 */
	public function testIsValidatedInConditionOnly( $testMarker, $expectedResult ) {
		$stackPtr = $this->getTargetToken( $testMarker, \T_VARIABLE );
		$result   = ValidationHelper::is_validated( self::$phpcsFile, $stackPtr, array( 'key' ), true );

		$this->assertSame( $expectedResult, $result );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsValidatedInConditionOnly()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsValidatedInConditionOnly() {
		return array(
			'use_outside_condition' => array(
				'testMarker'     => '/* testInConditionOnlyUseOutsideCondition */',
				'expectedResult' => false,
			),
			'use_inside_condition' => array(
				'testMarker'     => '/* testInConditionOnlyUseInsideCondition */',
				'expectedResult' => true,
			),
		);
	}

	/**
	 * Test is_validated() multi-level array key matching across validation paths.
	 *
	 * @dataProvider dataIsValidatedArrayKeys
	 *
	 * @param string       $testMarker     The comment which prefaces the target token in the test file.
	 * @param bool         $expectedResult The expected return value.
	 * @param array|string $array_keys     The array keys to check for.
	 *
	 * @return void
	 */
	public function testIsValidatedArrayKeys( $testMarker, $expectedResult, $array_keys ) {
		$stackPtr = $this->getTargetToken( $testMarker, \T_VARIABLE );
		$result   = ValidationHelper::is_validated( self::$phpcsFile, $stackPtr, $array_keys );

		$this->assertSame( $expectedResult, $result );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsValidatedArrayKeys()
	 *
	 * @return array<string, array<string, array<int, string>|bool|string>>
	 */
	public static function dataIsValidatedArrayKeys() {
		return array(
			'construct_subset' => array(
				'testMarker'     => '/* testArrayKeysConstruct */',
				'expectedResult' => true,
				'array_keys'     => array( 'key' ),
			),
			'construct_exact' => array(
				'testMarker'     => '/* testArrayKeysConstruct */',
				'expectedResult' => true,
				'array_keys'     => array( 'key', 'sub' ),
			),
			'construct_superset' => array(
				'testMarker'     => '/* testArrayKeysConstruct */',
				'expectedResult' => false,
				'array_keys'     => array( 'key', 'sub', 'deeper' ),
			),
			'construct_wrong_order' => array(
				'testMarker'     => '/* testArrayKeysConstruct */',
				'expectedResult' => false,
				'array_keys'     => array( 'sub', 'key' ),
			),
			'construct_string_key_valid' => array(
				'testMarker'     => '/* testArrayKeysConstruct */',
				'expectedResult' => true,
				'array_keys'     => 'key',
			),
			'construct_string_key_invalid' => array(
				'testMarker'     => '/* testArrayKeysConstruct */',
				'expectedResult' => false,
				'array_keys'     => 'other',
			),
			'construct_key_mismatch' => array(
				'testMarker'     => '/* testConstructParamKeyMismatch */',
				'expectedResult' => false,
				'array_keys'     => array( 'other' ),
			),
			'construct_empty_array_keys' => array(
				'testMarker'     => '/* testValidatedWithIsset */',
				'expectedResult' => true,
				'array_keys'     => array(),
			),
			'function_call_all_in_array_param' => array(
				'testMarker'     => '/* testArrayKeysFunctionCall */',
				'expectedResult' => true,
				'array_keys'     => array( 'key' ),
			),
			'function_call_split_keys' => array(
				'testMarker'     => '/* testArrayKeysFunctionCall */',
				'expectedResult' => true,
				'array_keys'     => array( 'key', 'sub' ),
			),
			'function_call_superset' => array(
				'testMarker'     => '/* testArrayKeysFunctionCall */',
				'expectedResult' => false,
				'array_keys'     => array( 'key', 'sub', 'deeper' ),
			),
			'function_call_wrong_order' => array(
				'testMarker'     => '/* testArrayKeysFunctionCall */',
				'expectedResult' => false,
				'array_keys'     => array( 'sub', 'key' ),
			),
			'function_call_key_param_mismatch' => array(
				'testMarker'     => '/* testArrayKeysFunctionCallKeyParamMismatch */',
				'expectedResult' => false,
				'array_keys'     => array( 'key', 'sub' ),
			),
			'function_call_empty_array_keys' => array(
				'testMarker'     => '/* testFunctionCallMixedCase */',
				'expectedResult' => true,
				'array_keys'     => array(),
			),
			'coalesce_subset' => array(
				'testMarker'     => '/* testArrayKeysCoalesce */',
				'expectedResult' => true,
				'array_keys'     => array( 'key' ),
			),
			'coalesce_exact' => array(
				'testMarker'     => '/* testArrayKeysCoalesce */',
				'expectedResult' => true,
				'array_keys'     => array( 'key', 'sub' ),
			),
			'coalesce_superset' => array(
				'testMarker'     => '/* testArrayKeysCoalesce */',
				'expectedResult' => false,
				'array_keys'     => array( 'key', 'sub', 'deeper' ),
			),
			'coalesce_wrong_order' => array(
				'testMarker'     => '/* testArrayKeysCoalesce */',
				'expectedResult' => false,
				'array_keys'     => array( 'sub', 'key' ),
			),
		);
	}

	/**
	 * Parse a separate .inc file and assert the result of is_validated().
	 *
	 * Temporarily swaps self::$phpcsFile so that getTargetToken() works on the parsed file.
	 *
	 * @param string $testCaseFile    The test case file to parse.
	 * @param bool   $expectedResult  The expected return value.
	 * @param bool   $inConditionOnly What to pass as the $in_condition_only parameter.
	 *
	 * @return void
	 */
	private function assertIsValidatedInFile( $testCaseFile, $expectedResult, $inConditionOnly = false ) {
		$originalFile    = self::$phpcsFile;
		self::$phpcsFile = self::parseFile(
			__DIR__ . '/' . $testCaseFile,
			$originalFile->ruleset,
			$originalFile->config
		);

		$stackPtr = $this->getTargetToken( '/* testValidationTarget */', \T_VARIABLE );

		$this->assertSame(
			$expectedResult,
			ValidationHelper::is_validated( self::$phpcsFile, $stackPtr, array( 'key' ), $inConditionOnly )
		);

		self::$phpcsFile = $originalFile;
	}
}
