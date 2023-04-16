<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;

/**
 * Helper utilities for checking the context in which a token is used.
 *
 * ---------------------------------------------------------------------------------------------
 * This class is only intended for internal use by WordPressCS and is not part of the public API.
 * This also means that it has no promise of backward compatibility. Use at your own risk.
 * ---------------------------------------------------------------------------------------------
 *
 * @package WPCS\WordPressCodingStandards
 * @since   3.0.0 The methods in this class were previously contained in the
 *                `WordPressCS\WordPress\Sniff` class and have been moved here.
 */
final class ContextHelper {

	/**
	 * List of PHP native functions to test the type of a variable.
	 *
	 * Using these functions is safe in combination with superglobals without
	 * unslashing or sanitization.
	 *
	 * They should, however, not be regarded as unslashing or sanitization functions.
	 *
	 * @since 2.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The property visibility was changed from `protected` to `private static`.
	 *
	 * @var array
	 */
	private static $typeTestFunctions = array(
		'is_array'     => true,
		'is_bool'      => true,
		'is_callable'  => true,
		'is_countable' => true,
		'is_double'    => true,
		'is_float'     => true,
		'is_int'       => true,
		'is_integer'   => true,
		'is_iterable'  => true,
		'is_long'      => true,
		'is_null'      => true,
		'is_numeric'   => true,
		'is_object'    => true,
		'is_real'      => true,
		'is_resource'  => true,
		'is_scalar'    => true,
		'is_string'    => true,
	);

	/**
	 * Check if a particular token acts - statically or non-statically - on an object.
	 *
	 * @internal Note: this may still mistake a namespaced function imported via a `use` statement for
	 * a global function!
	 *
	 * @since 2.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method visibility was changed from `protected` to `public static`.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The index of the token in the stack.
	 *
	 * @return bool
	 */
	public static function has_object_operator_before( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$before = $phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true );

		return isset( Collections::objectOperators()[ $tokens[ $before ]['code'] ] );
	}

	/**
	 * Check if a particular token is prefixed with a namespace.
	 *
	 * @internal This will give a false positive if the file is not namespaced and the token is prefixed
	 * with `namespace\`.
	 *
	 * @since 2.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method visibility was changed from `protected` to `public static`.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The index of the token in the stack.
	 *
	 * @return bool
	 */
	public static function is_token_namespaced( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$prev   = $phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true );

		if ( \T_NS_SEPARATOR !== $tokens[ $prev ]['code'] ) {
			return false;
		}

		$before_prev = $phpcsFile->findPrevious( Tokens::$emptyTokens, ( $prev - 1 ), null, true );
		if ( \T_STRING !== $tokens[ $before_prev ]['code']
			&& \T_NAMESPACE !== $tokens[ $before_prev ]['code']
		) {
			return false;
		}

		return true;
	}

	/**
	 * Check if a token is (part of) a parameter for a function call to a select list of functions.
	 *
	 * This is useful, for instance, when trying to determine the context a variable is used in.
	 *
	 * For example: this function could be used to determine if the variable `$foo` is used
	 * in a global function call to the function `is_foo()`.
	 * In that case, a call to this function would return the stackPtr to the T_STRING `is_foo`
	 * for code like: `is_foo( $foo, 'some_other_param' )`, while it would return `false` for
	 * the following code `is_bar( $foo, 'some_other_param' )`.
	 *
	 * @since 2.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method visibility was changed from `protected` to `public static`.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile       The file being scanned.
	 * @param int                         $stackPtr        The index of the token in the stack.
	 * @param array                       $valid_functions List of valid function names.
	 *                                                     Note: The keys to this array should be the function names
	 *                                                     in lowercase. Values are irrelevant.
	 * @param bool                        $global_function Optional. Whether to make sure that the function call is
	 *                                                     to a global function. If `false`, calls to methods, be it static
	 *                                                     `Class::method()` or via an object `$obj->method()`, and
	 *                                                     namespaced function calls, like `MyNS\function_name()` will
	 *                                                     also be accepted.
	 *                                                     Defaults to `true`.
	 * @param bool                        $allow_nested    Optional. Whether to allow for nested function calls within the
	 *                                                     call to this function.
	 *                                                     I.e. when checking whether a token is within a function call
	 *                                                     to `strtolower()`, whether to accept `strtolower( trim( $var ) )`
	 *                                                     or only `strtolower( $var )`.
	 *                                                     Defaults to `false`.
	 *
	 * @return int|bool Stack pointer to the function call T_STRING token or false otherwise.
	 */
	public static function is_in_function_call( File $phpcsFile, $stackPtr, array $valid_functions, $global_function = true, $allow_nested = false ) {
		$tokens = $phpcsFile->getTokens();
		if ( ! isset( $tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			return false;
		}

		$nested_parenthesis = $tokens[ $stackPtr ]['nested_parenthesis'];
		if ( false === $allow_nested ) {
			$nested_parenthesis = array_reverse( $nested_parenthesis, true );
		}

		foreach ( $nested_parenthesis as $open => $close ) {
			$prev_non_empty = $phpcsFile->findPrevious( Tokens::$emptyTokens, ( $open - 1 ), null, true );
			if ( false === $prev_non_empty || \T_STRING !== $tokens[ $prev_non_empty ]['code'] ) {
				continue;
			}

			if ( isset( $valid_functions[ strtolower( $tokens[ $prev_non_empty ]['content'] ) ] ) === false ) {
				if ( false === $allow_nested ) {
					// Function call encountered, but not to one of the allowed functions.
					return false;
				}

				continue;
			}

			if ( false === $global_function ) {
				return $prev_non_empty;
			}

			/*
			 * Now, make sure it is a global function.
			 */
			if ( self::has_object_operator_before( $phpcsFile, $prev_non_empty ) === true ) {
				continue;
			}

			if ( self::is_token_namespaced( $phpcsFile, $prev_non_empty ) === true ) {
				continue;
			}

			return $prev_non_empty;
		}

		return false;
	}

	/**
	 * Check if a token is inside of an is_...() statement.
	 *
	 * @since 2.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method visibility was changed from `protected` to `public static`.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The index of the token in the stack.
	 *
	 * @return bool Whether the token is being type tested.
	 */
	public static function is_in_type_test( File $phpcsFile, $stackPtr ) {
		/*
		 * Casting the potential integer stack pointer return value to boolean here is fine.
		 * The return can never be `0` as there will always be a PHP open tag before the
		 * function call.
		 */
		return (bool) self::is_in_function_call( $phpcsFile, $stackPtr, self::$typeTestFunctions );
	}
}
