<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\WPDBTrait;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the `WPDBTrait::is_wpdb_method_call()` utility method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\WPDBTrait::is_wpdb_method_call
 */
final class IsWpdbMethodCallUnitTest extends UtilityMethodTestCase {

	/**
	 * Test helper class using the WPDBTrait.
	 *
	 * @var WPDBTraitWrapper
	 */
	private $testClass;

	/**
	 * Set up a fresh test class instance for each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->testClass = new WPDBTraitWrapper();
	}

	/**
	 * Test is_wpdb_method_call() returns false if the token does not exist.
	 *
	 * @return void
	 */
	public function testIsWpdbMethodCallReturnsFalseIfTokenDoesNotExist() {
		$result = $this->testClass->invoke_is_wpdb_method_call(
			self::$phpcsFile,
			-1,
			array( 'prepare' => true )
		);

		$this->assertFalse( $result );
	}

	/**
	 * Test is_wpdb_method_call() returns false when $target_methods is empty.
	 *
	 * @return void
	 */
	public function testIsWpdbMethodCallReturnsFalseWithEmptyTargetMethods() {
		$stackPtr = $this->getTargetToken( '/* testObjectOperator */', \T_VARIABLE );
		$result   = $this->testClass->invoke_is_wpdb_method_call(
			self::$phpcsFile,
			$stackPtr,
			array()
		);

		$this->assertFalse( $result );
	}

	/**
	 * Test is_wpdb_method_call() returns false for live coding / parse error situations.
	 *
	 * @dataProvider dataIsWpdbMethodCallLiveCoding
	 *
	 * @param string $caseFile The test case file to parse.
	 *
	 * @return void
	 */
	public function testIsWpdbMethodCallLiveCoding( $caseFile ) {
		$phpcsFile = self::parseFile(
			__DIR__ . '/' . $caseFile,
			self::$phpcsFile->ruleset,
			self::$phpcsFile->config
		);

		$stackPtr = $phpcsFile->findNext( \T_VARIABLE, 0 );
		$this->assertNotFalse( $stackPtr );

		$result = $this->testClass->invoke_is_wpdb_method_call(
			$phpcsFile,
			$stackPtr,
			array( 'prepare' => true )
		);

		$this->assertFalse( $result );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsWpdbMethodCallLiveCoding()
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function dataIsWpdbMethodCallLiveCoding() {
		return array(
			'no_object_operator' => array(
				'caseFile' => 'IsWpdbMethodCallNoObjectOperatorUnitTest.inc',
			),
			'no_method_name' => array(
				'caseFile' => 'IsWpdbMethodCallNoMethodNameUnitTest.inc',
			),
			'no_opening_parenthesis' => array(
				'caseFile' => 'IsWpdbMethodCallNoOpeningParenthesisUnitTest.inc',
			),
			'unclosed_parenthesis' => array(
				'caseFile' => 'IsWpdbMethodCallUnclosedParenthesisUnitTest.inc',
			),
		);
	}

	/**
	 * Test is_wpdb_method_call().
	 *
	 * @dataProvider dataIsWpdbMethodCall
	 *
	 * @param string      $testMarker      The comment which prefaces the target token in the test file.
	 * @param bool        $expectedResult  The expected return value.
	 * @param int|string  $tokenType       The token type to search for.
	 * @param string|null $tokenContent    The token content to search for (if applicable).
	 * @param bool        $hasMethodPtr    Whether the methodPtr property should be set.
	 * @param string|bool $openParenMarker The test marker for the expected i token, or false if not expected to be set.
	 * @param bool        $hasEnd          Whether the end property should be set.
	 *
	 * @return void
	 */
	public function testIsWpdbMethodCall(
		$testMarker,
		$expectedResult,
		$tokenType,
		$tokenContent = null,
		$hasMethodPtr = false,
		$openParenMarker = false,
		$hasEnd = false
	) {
		$stackPtr = $this->getTargetToken( $testMarker, $tokenType, $tokenContent );
		$result   = $this->testClass->invoke_is_wpdb_method_call(
			self::$phpcsFile,
			$stackPtr,
			array(
				'prepare'  => true,
				'esc_like' => true,
			)
		);

		$this->assertSame( $expectedResult, $result );

		if ( true === $hasMethodPtr ) {
			$expectedPtr = self::$phpcsFile->findNext( \T_STRING, ( $stackPtr + 1 ) );
			$this->assertSame( $expectedPtr, $this->testClass->methodPtr );
		} else {
			$this->assertNull( $this->testClass->methodPtr );
		}

		if ( false !== $openParenMarker ) {
			$expectedIPtr = $this->getTargetToken( $openParenMarker, array( \T_OPEN_PARENTHESIS, \T_SEMICOLON ) );
			$this->assertSame( $expectedIPtr, $this->testClass->i );
		} else {
			$this->assertNull( $this->testClass->i );
		}

		// Note: the exact value of end is not verified because it depends on the result of
		// BCFile::findEndOfStatement() which would require reimplementing the method's logic in the test.
		if ( true === $hasEnd ) {
			$this->assertIsInt( $this->testClass->end );
		} else {
			$this->assertNull( $this->testClass->end );
		}
	}

	/**
	 * Data provider.
	 *
	 * @see testIsWpdbMethodCall()
	 *
	 * @return array<string, array<string, bool|int|string|null>>
	 */
	public static function dataIsWpdbMethodCall() {
		return array(
			// Cases that should return false.
			'not_wpdb_variable' => array(
				'testMarker'     => '/* testNotWpdbVariable */',
				'expectedResult' => false,
				'tokenType'      => \T_VARIABLE,
			),
			'not_wpdb_class' => array(
				'testMarker'     => '/* testNotWpdbClass */',
				'expectedResult' => false,
				'tokenType'      => \T_STRING,
			),
			'wpdb_not_method_call' => array(
				'testMarker'     => '/* testWpdbNotMethodCall */',
				'expectedResult' => false,
				'tokenType'      => \T_VARIABLE,
			),
			'property_access' => array(
				'testMarker'      => '/* testPropertyAccess */',
				'expectedResult'  => false,
				'tokenType'       => \T_VARIABLE,
				'tokenContent'    => null,
				'hasMethodPtr'    => true,
				'openParenMarker' => '/* testPropertyAccessOpenParen */',
			),
			'function_call' => array(
				'testMarker'     => '/* testFunctionCall */',
				'expectedResult' => false,
				'tokenType'      => \T_STRING,
			),
			'partially_qualified' => array(
				'testMarker'     => '/* testPartiallyQualified */',
				'expectedResult' => false,
				'tokenType'      => \T_STRING,
				'tokenContent'   => 'wpdb',
			),
			'fully_qualified_namespaced' => array(
				'testMarker'     => '/* testFullyQualifiedNamespaced */',
				'expectedResult' => false,
				'tokenType'      => \T_STRING,
				'tokenContent'   => 'wpdb',
			),
			'namespace_relative' => array(
				'testMarker'     => '/* testNamespaceRelative */',
				'expectedResult' => false,
				'tokenType'      => \T_STRING,
				'tokenContent'   => 'wpdb',
			),
			'namespace_relative_sub' => array(
				'testMarker'     => '/* testNamespaceRelativeSub */',
				'expectedResult' => false,
				'tokenType'      => \T_STRING,
				'tokenContent'   => 'wpdb',
			),
			'not_target_method' => array(
				'testMarker'      => '/* testNotTargetMethod */',
				'expectedResult'  => false,
				'tokenType'       => \T_VARIABLE,
				'tokenContent'    => null,
				'hasMethodPtr'    => true,
				'openParenMarker' => '/* testNotTargetMethodOpenParen */',
			),
			'uppercase_wpdb_variable' => array(
				'testMarker'     => '/* testUppercaseWpdbVariable */',
				'expectedResult' => false,
				'tokenType'      => \T_VARIABLE,
			),
			'variable_method_call' => array(
				'testMarker'      => '/* testVariableMethodCall */',
				'expectedResult'  => false,
				'tokenType'       => \T_VARIABLE,
				'tokenContent'    => '$wpdb',
				'hasMethodPtr'    => false,
				'openParenMarker' => '/* testVariableMethodCallOpenParen */',
			),

			// Cases that should return true.
			'object_operator' => array(
				'testMarker'      => '/* testObjectOperator */',
				'expectedResult'  => true,
				'tokenType'       => \T_VARIABLE,
				'tokenContent'    => null,
				'hasMethodPtr'    => true,
				'openParenMarker' => '/* testObjectOperatorOpenParen */',
				'hasEnd'          => true,
			),
			'nullsafe_object_operator' => array(
				'testMarker'      => '/* testNullsafeObjectOperator */',
				'expectedResult'  => true,
				'tokenType'       => \T_VARIABLE,
				'tokenContent'    => null,
				'hasMethodPtr'    => true,
				'openParenMarker' => '/* testNullsafeObjectOperatorOpenParen */',
				'hasEnd'          => true,
			),
			'unqualified_class_uppercase' => array(
				'testMarker'      => '/* testUnqualifiedClassUppercase */',
				'expectedResult'  => true,
				'tokenType'       => \T_STRING,
				'tokenContent'    => null,
				'hasMethodPtr'    => true,
				'openParenMarker' => '/* testUnqualifiedClassUppercaseOpenParen */',
				'hasEnd'          => true,
			),
			'unqualified_class_lowercase' => array(
				'testMarker'      => '/* testUnqualifiedClassLowercase */',
				'expectedResult'  => true,
				'tokenType'       => \T_STRING,
				'tokenContent'    => null,
				'hasMethodPtr'    => true,
				'openParenMarker' => '/* testUnqualifiedClassLowercaseOpenParen */',
				'hasEnd'          => true,
			),
			'fully_qualified_global_lowercase' => array(
				'testMarker'      => '/* testFullyQualifiedGlobalLowercase */',
				'expectedResult'  => true,
				'tokenType'       => \T_STRING,
				'tokenContent'    => null,
				'hasMethodPtr'    => true,
				'openParenMarker' => '/* testFullyQualifiedGlobalLowercaseOpenParen */',
				'hasEnd'          => true,
			),
			'fully_qualified_global_uppercase' => array(
				'testMarker'      => '/* testFullyQualifiedGlobalUppercase */',
				'expectedResult'  => true,
				'tokenType'       => \T_STRING,
				'tokenContent'    => null,
				'hasMethodPtr'    => true,
				'openParenMarker' => '/* testFullyQualifiedGlobalUppercaseOpenParen */',
				'hasEnd'          => true,
			),
		);
	}
}
