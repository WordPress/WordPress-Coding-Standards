<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Util\Tests\Helpers\ContextHelper;

use PHPCSUtils\Tokens\Collections;
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
	 * Test is_in_function_call() returns false if given token is not inside a function call
	 * or not inside one of the expected function calls.
	 *
	 * @dataProvider dataIsInFunctionCallShouldReturnFalse
	 *
	 * @param string     $commentString The comment which prefaces the target token in the test file.
	 * @param int|string $tokenType     The token type to search for.
	 *
	 * @return void
	 */
	public function testIsInFunctionCallShouldReturnFalse( $commentString, $tokenType ) {
		$stackPtr = $this->getTargetToken( $commentString, $tokenType );
		$result   = ContextHelper::is_in_function_call( self::$phpcsFile, $stackPtr, array( 'my_function' => true ) );
		$this->assertFalse( $result );
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 * @see testIsInFunctionCallShouldReturnFalse()
	 */
	public static function dataIsInFunctionCallShouldReturnFalse() {
		return array(
			array( '/* test return false 1 */', \T_CONSTANT_ENCAPSED_STRING ),
			array( '/* test return false 2 */', \T_VARIABLE ),
			array( '/* test return false 3 */', \T_VARIABLE ),
			array( '/* test return false 4 */', \T_VARIABLE ),
			array( '/* test return false 5 */', \T_VARIABLE ),
			array( '/* test return false 6 */', \T_VARIABLE ),
			array( '/* test return false 7 */', \T_VARIABLE ),
			array( '/* test return false 8 */', \T_VARIABLE ),
		);
	}

	/**
	 * Test is_in_function_call() returns pointer to function name if given token is inside a function call.
	 *
	 * @dataProvider dataIsInFunctionCallShouldReturnFunctionPointer
	 *
	 * @param string     $insideFunctionCommentString The comment which prefaces the target token inside a function in the test file.
	 * @param int|string $insideFunctionTokenType     The token type to search for.
	 * @param string     $functionCallCommentString   The comment which prefaces the function call token in the test file.
	 *
	 * @return void
	 */
	public function testIsInFunctionCallShouldReturnFunctionPointer( $insideFunctionCommentString, $insideFunctionTokenType, $functionCallCommentString ) {
		$insideFunctionPtr = $this->getTargetToken( $insideFunctionCommentString, $insideFunctionTokenType );
		$functionNamePtr   = $this->getTargetToken( $functionCallCommentString, Collections::nameTokens() );
		$result            = ContextHelper::is_in_function_call( self::$phpcsFile, $insideFunctionPtr, array( 'my_function' => true ) );
		$this->assertSame( $result, $functionNamePtr );
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 * @see testIsInFunctionCallShouldReturnFunctionPointer()
	 */
	public static function dataIsInFunctionCallShouldReturnFunctionPointer() {
		return array(
			array( '/* test inside function pointer 1 */', \T_VARIABLE, '/* test function call 1 */' ),
			array( '/* test inside function pointer 2 */', \T_VARIABLE, '/* test function call 2 */' ),
			array( '/* test inside function pointer 3 */', \T_VARIABLE, '/* test function call 3 */' ),
		);
	}

	/**
	 * Test is_in_function_call() returns pointer to function name if given token is inside a
	 * function call when `$global_functions` is set to false.
	 *
	 * @dataProvider dataIsInFunctionCallShouldReturnFunctionPointerWhenGlobalIsFalse
	 *
	 * @param string     $insideFunctionCommentString The comment which prefaces the target token inside a function in the test file.
	 * @param int|string $insideFunctionTokenType     The token type to search for.
	 * @param string     $functionCallCommentString   The comment which prefaces the function call token in the test file.
	 *
	 * @return void
	 */
	public function testIsInFunctionCallShouldReturnFunctionPointerWhenGlobalIsFalse( $insideFunctionCommentString, $insideFunctionTokenType, $functionCallCommentString ) {
		$insideFunctionPtr = $this->getTargetToken( $insideFunctionCommentString, $insideFunctionTokenType );
		$functionNamePtr   = $this->getTargetToken( $functionCallCommentString, Collections::nameTokens() );
		$result            = ContextHelper::is_in_function_call( self::$phpcsFile, $insideFunctionPtr, array( 'my_function' => true ), false );
		$this->assertSame( $result, $functionNamePtr );
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 * @see testIsInFunctionCallShouldReturnFunctionPointerWhenGlobalIsFalse()
	 */
	public static function dataIsInFunctionCallShouldReturnFunctionPointerWhenGlobalIsFalse() {
		return array(
			array( '/* test inside function pointer 4 */', \T_VARIABLE, '/* test function call 4 */' ),
			array( '/* test inside function pointer 5 */', \T_VARIABLE, '/* test function call 5 */' ),
			array( '/* test inside function pointer 6 */', \T_VARIABLE, '/* test function call 6 */' ),
			array( '/* test inside function pointer 7 */', \T_VARIABLE, '/* test function call 7 */' ),
			array( '/* test inside function pointer 8 */', \T_VARIABLE, '/* test function call 8 */' ),
			array( '/* test inside function pointer 9 */', \T_VARIABLE, '/* test function call 9 */' ),
			array( '/* test inside function pointer 10 */', \T_VARIABLE, '/* test function call 10 */' ),
			array( '/* test inside function pointer 11 */', \T_VARIABLE, '/* test function call 11 */' ),
			array( '/* test inside function pointer 12 */', \T_VARIABLE, '/* test function call 12 */' ),
		);
	}

	/**
	 * Test is_in_function_call() returns pointer to function name if given token is inside a
	 * function call when `$allow_nested` is set to true.
	 *
	 * @dataProvider dataIsInFunctionCallShouldReturnFunctionPointerWhenAllowNestedIsTrue
	 *
	 * @param string     $insideFunctionCommentString The comment which prefaces the target token inside a function in the test file.
	 * @param int|string $insideFunctionTokenType     The token type to search for.
	 * @param string     $functionCallCommentString   The comment which prefaces the function call token in the test file.
	 *
	 * @return void
	 */
	public function testIsInFunctionCallShouldReturnFunctionPointerWhenAllowNestedIsTrue( $insideFunctionCommentString, $insideFunctionTokenType, $functionCallCommentString ) {
		$insideFunctionPtr = $this->getTargetToken( $insideFunctionCommentString, $insideFunctionTokenType );
		$functionNamePtr   = $this->getTargetToken( $functionCallCommentString, Collections::nameTokens() );
		$result            = ContextHelper::is_in_function_call( self::$phpcsFile, $insideFunctionPtr, array( 'my_function' => true ), true, true );
		$this->assertSame( $result, $functionNamePtr );
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 * @see testIsInFunctionCallShouldReturnFunctionPointerWhenAllowNestedIsTrue()
	 */
	public static function dataIsInFunctionCallShouldReturnFunctionPointerWhenAllowNestedIsTrue() {
		return array(
			array( '/* test inside function pointer 13 */', \T_VARIABLE, '/* test function call 13 */' ),
			array( '/* test inside function pointer 14 */', \T_VARIABLE, '/* test function call 14 */' ),
			array( '/* test inside function pointer 15 */', \T_VARIABLE, '/* test function call 15 */' ),
		);
	}
}
