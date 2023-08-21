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
use PHPCSUtils\Utils\Parentheses;
use PHPCSUtils\Utils\PassedParameters;

/**
 * Helper utilities for checking the context in which a token is used.
 *
 * ---------------------------------------------------------------------------------------------
 * This class is only intended for internal use by WordPressCS and is not part of the public API.
 * This also means that it has no promise of backward compatibility. Use at your own risk.
 * ---------------------------------------------------------------------------------------------
 *
 * @internal
 *
 * @since 3.0.0 The methods in this class were previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and have been moved here.
 */
final class ContextHelper {

	/**
	 * Tokens which when they preceed code indicate the value is safely casted.
	 *
	 * @since 1.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The property visibility was changed from `protected` to `private static`.
	 *
	 * @var array<int|string, true> Key is token constant, value irrelevant.
	 */
	private static $safe_casts = array(
		\T_INT_CAST    => true,
		\T_DOUBLE_CAST => true,
		\T_BOOL_CAST   => true,
		\T_UNSET_CAST  => true,
	);

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
	 * @var array<string, true> Key is function name, value irrelevant.
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
	 * List of PHP native functions to check if an array index exists.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, true> Key is function name, value irrelevant.
	 */
	private static $key_exists_functions = array(
		'array_key_exists' => true,
		'key_exists'       => true, // Alias.
	);

	/**
	 * Array functions to compare a $needle to a predefined set of values.
	 *
	 * If the value is set to an array, the parameter specified in the array is
	 * required for the function call to be considered as a comparison.
	 *
	 * @since 2.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The property visibility was changed from `protected` to `private static`.
	 *
	 * @var array<string, bool|array>
	 */
	private static $arrayCompareFunctions = array(
		'in_array'     => true,
		'array_search' => true,
		'array_keys'   => array(
			'position' => 2,
			'name'     => 'filter_value',
		),
	);

	/**
	 * Check if a particular token acts - statically or non-statically - on an object.
	 *
	 * {@internal Note: this may still mistake a namespaced function imported via a `use` statement for
	 * a global function!}
	 *
	 * @since 2.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method was renamed from `is_class_object_call() to `has_object_operator_before()`.
	 *              - The method visibility was changed from `protected` to `public static`.
	 *              - The `$phpcsFile` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The index of the token in the stack.
	 *
	 * @return bool
	 */
	public static function has_object_operator_before( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		if ( isset( $tokens[ $stackPtr ] ) === false ) {
			return false;
		}

		$before = $phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true );

		return isset( Collections::objectOperators()[ $tokens[ $before ]['code'] ] );
	}

	/**
	 * Check if a particular token is prefixed with a namespace.
	 *
	 * {@internal This will give a false positive if the file is not namespaced and the token is prefixed
	 * with `namespace\`.}
	 *
	 * @since 2.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method visibility was changed from `protected` to `public static`.
	 *              - The `$phpcsFile` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The index of the token in the stack.
	 *
	 * @return bool
	 */
	public static function is_token_namespaced( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		if ( isset( $tokens[ $stackPtr ] ) === false ) {
			return false;
		}

		$prev = $phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true );

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
	 *              - The `$phpcsFile` parameter was added.
	 *              - The `$global` parameter was renamed to `$global_function`.
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
	 *              - The `$phpcsFile` parameter was added.
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

	/**
	 * Check if a token is inside of an isset(), empty() or array_key_exists() statement.
	 *
	 * @since 0.5.0
	 * @since 2.1.0 Now checks for the token being used as the array parameter
	 *              in function calls to array_key_exists() and key_exists() as well.
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method visibility was changed from `protected` to `public static`.
	 *              - The `$phpcsFile` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The index of the token in the stack.
	 *
	 * @return bool Whether the token is inside an isset() or empty() statement.
	 */
	public static function is_in_isset_or_empty( File $phpcsFile, $stackPtr ) {
		if ( Parentheses::lastOwnerIn( $phpcsFile, $stackPtr, array( \T_ISSET, \T_EMPTY ) ) !== false ) {
			return true;
		}

		$functionPtr = self::is_in_function_call( $phpcsFile, $stackPtr, self::$key_exists_functions );
		if ( false !== $functionPtr ) {
			/*
			 * Both functions being checked have the same parameters. If the function list would
			 * be expanded, this needs to be revisited.
			 */
			$array_param = PassedParameters::getParameter( $phpcsFile, $functionPtr, 2, 'array' );
			if ( false !== $array_param
				&& ( $stackPtr >= $array_param['start'] && $stackPtr <= $array_param['end'] )
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Retrieve a list of the tokens which are regarded as "safe casts".
	 *
	 * @since 3.0.0
	 *
	 * @return array<int|string, true>
	 */
	public static function get_safe_cast_tokens() {
		return self::$safe_casts;
	}

	/**
	 * Check if something is being casted to a safe value.
	 *
	 * @since 0.5.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method visibility was changed from `protected` to `public static`.
	 *              - The `$phpcsFile` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The index of the token in the stack.
	 *
	 * @return bool Whether the token being casted.
	 */
	public static function is_safe_casted( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		if ( isset( $tokens[ $stackPtr ] ) === false ) {
			return false;
		}

		$prev = $phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true );

		return isset( self::$safe_casts[ $tokens[ $prev ]['code'] ] );
	}

	/**
	 * Check if a token is inside of an array-value comparison function.
	 *
	 * @since 2.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method visibility was changed from `protected` to `public static`.
	 *              - The `$phpcsFile` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The index of the token in the stack.
	 *
	 * @return bool Whether the token is (part of) a parameter to an
	 *              array-value comparison function.
	 */
	public static function is_in_array_comparison( File $phpcsFile, $stackPtr ) {
		$function_ptr = self::is_in_function_call( $phpcsFile, $stackPtr, self::$arrayCompareFunctions, true, true );
		if ( false === $function_ptr ) {
			return false;
		}

		$tokens        = $phpcsFile->getTokens();
		$function_name = strtolower( $tokens[ $function_ptr ]['content'] );
		if ( true === self::$arrayCompareFunctions[ $function_name ] ) {
			return true;
		}

		$target_param = self::$arrayCompareFunctions[ $function_name ];
		$found_param  = PassedParameters::getParameter( $phpcsFile, $function_ptr, $target_param['position'], $target_param['name'] );
		if ( false !== $found_param ) {
			return true;
		}

		return false;
	}
}
