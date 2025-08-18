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
 * Tests for the `ContextHelper::is_token_namespaced()` utility method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\ContextHelper::is_token_namespaced
 */
final class IsTokenNamespacedUnitTest extends UtilityMethodTestCase {

	/**
	 * Test is_token_namespaced() returns false if the token does not exist.
	 *
	 * @return void
	 */
	public function testIsTokenNamespacedReturnsFalseIfTokenDoesNotExist() {
		$this->assertFalse( ContextHelper::is_token_namespaced( self::$phpcsFile, -1 ) );
	}

	/**
	 * Test is_token_namespaced().
	 *
	 * @dataProvider dataIsTokenNamespaced
	 *
	 * @param string $testMarker     The comment which prefaces the target token in the test file.
	 * @param bool   $expectedResult The expected return value.
	 * @param string $tokenContent   The token content to use for the target token.
	 *
	 * @return void
	 */
	public function testIsTokenNamespaced( $testMarker, $expectedResult, $tokenContent ) {
		$stackPtr = $this->getTargetToken( $testMarker, \T_STRING, $tokenContent );
		$result   = ContextHelper::is_token_namespaced( self::$phpcsFile, $stackPtr );

		$this->assertSame( $expectedResult, $result );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsTokenNamespaced()
	 *
	 * @return array<string, array<string, bool|string>>
	 */
	public static function dataIsTokenNamespaced() {
		return array(
			// Cases that should return false.
			'unqualified' => array(
				'testMarker'     => '/* testUnqualified */',
				'expectedResult' => false,
				'tokenContent'   => 'my_function',
			),
			'fully_qualified' => array(
				'testMarker'     => '/* testFullyQualified */',
				'expectedResult' => false,
				'tokenContent'   => 'MY_CONSTANT',
			),

			// Cases that should return true.
			'partially_qualified' => array(
				'testMarker'     => '/* testPartiallyQualified */',
				'expectedResult' => true,
				'tokenContent'   => 'my_function',
			),
			'fully_qualified_namespaced' => array(
				'testMarker'     => '/* testFullyQualifiedNamespaced */',
				'expectedResult' => true,
				'tokenContent'   => 'MyClass',
			),
			'namespace_relative' => array(
				'testMarker'     => '/* testNamespaceRelative */',
				'expectedResult' => true,
				'tokenContent'   => 'MY_CONSTANT',
			),
			'namespace_relative_sub' => array(
				'testMarker'     => '/* testNamespaceRelativeSub */',
				'expectedResult' => true,
				'tokenContent'   => 'my_function',
			),
		);
	}
}
