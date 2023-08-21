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
use PHPCSUtils\Utils\Conditions;
use PHPCSUtils\Utils\Context;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\Helpers\ContextHelper;
use WordPressCS\WordPress\Helpers\VariableHelper;

/**
 * Helper function for checking whether a token is validated.
 *
 * @since 3.0.0 The method in this class was previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class ValidationHelper {

	/**
	 * The tokens the function looks for to determine whether a token is validated.
	 *
	 * @since 3.0.0
	 *
	 * @var array<int|string, string>
	 */
	private static $targets = array(
		\T_ISSET          => 'construct',
		\T_EMPTY          => 'construct',
		\T_STRING         => 'function_call',
		\T_COALESCE       => 'coalesce',
		\T_COALESCE_EQUAL => 'coalesce',
	);

	/**
	 * List of PHP native functions to check if an array index exists.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, bool>
	 */
	private static $key_exists_functions = array(
		'array_key_exists' => true,
		'key_exists'       => true, // Alias.
	);

	/**
	 * Check if the existence of a variable is validated with isset(), empty(), array_key_exists()
	 * or key_exists().
	 *
	 * When $in_condition_only is `false`, (which is the default), this is considered
	 * valid:
	 *
	 * ```php
	 * if ( isset( $var ) ) {
	 *     // Do stuff, like maybe return or exit (but could be anything)
	 * }
	 *
	 * foo( $var );
	 * ```
	 *
	 * When it is `true`, that would be invalid; the use of the variable must be within
	 * the scope of the validating condition, like this:
	 *
	 * ```php
	 * if ( isset( $var ) ) {
	 *     foo( $var );
	 * }
	 * ```
	 *
	 * @since 0.5.0
	 * @since 2.1.0 Now recognizes array_key_exists() and key_exists() as validation functions.
	 * @since 2.1.0 Stricter check on whether the correct variable and the correct
	 *              array keys are being validated.
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method visibility was changed from `protected` to `public static`.
	 *              - The `$phpcsFile` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile         The file being scanned.
	 * @param int                         $stackPtr          The index of this token in the stack.
	 * @param array|string                $array_keys        An array key to check for ("bar" in $foo['bar'])
	 *                                                       or an array of keys for multi-level array access.
	 * @param bool                        $in_condition_only Whether to require that this use of the
	 *                                                       variable occurs within the scope of the
	 *                                                       validating condition, or just in the same
	 *                                                       scope (default).
	 *
	 * @return bool Whether the var is validated.
	 */
	public static function is_validated( File $phpcsFile, $stackPtr, $array_keys = array(), $in_condition_only = false ) {
		$tokens = $phpcsFile->getTokens();
		if ( isset( $tokens[ $stackPtr ] ) === false ) {
			return false;
		}

		if ( $in_condition_only ) {
			/*
			 * This is a stricter check, requiring the variable to be used only
			 * within the validation condition.
			 */
			$conditionPtr = Conditions::getLastCondition( $phpcsFile, $stackPtr );
			if ( false === $conditionPtr ) {
				// If there are no conditions, there's no validation.
				return false;
			}

			$condition = $tokens[ $conditionPtr ];
			if ( ! isset( $condition['parenthesis_opener'] ) ) {
				// Live coding or parse error.
				return false;
			}

			$scope_start = $condition['parenthesis_opener'];
			$scope_end   = $condition['parenthesis_closer'];

		} else {
			/*
			 * We are more loose, requiring only that the variable be validated
			 * in the same function/file scope as it is used.
			 */
			$scope_start = 0;

			/*
			 * Check if we are in a function.
			 *
			 * Note: PHP 7.4+ arrow functions are not taken into account as those are not
			 * included in the "conditions" array. Additionally, arrow functions have
			 * access to variables outside their direct scope.
			 */
			$function = Conditions::getLastCondition( $phpcsFile, $stackPtr, array( \T_FUNCTION, \T_CLOSURE ) );

			// If so, we check only within the function, otherwise the whole file.
			if ( false !== $function ) {
				$scope_start = $tokens[ $function ]['scope_opener'];
			}

			$scope_end = $stackPtr;
		}

		if ( ! empty( $array_keys ) && ! is_array( $array_keys ) ) {
			$array_keys = (array) $array_keys;
		}

		$bare_array_keys = self::strip_quotes_from_array_values( $array_keys );

		// phpcs:ignore Generic.CodeAnalysis.JumbledIncrementer.Found -- On purpose, see below.
		for ( $i = ( $scope_start + 1 ); $i < $scope_end; $i++ ) {

			if ( isset( Collections::closedScopes()[ $tokens[ $i ]['code'] ] )
				&& isset( $tokens[ $i ]['scope_closer'] )
			) {
				// Jump over nested closed scopes as validation done within those does not apply.
				$i = $tokens[ $i ]['scope_closer'];
				continue;
			}

			if ( \T_FN === $tokens[ $i ]['code']
				&& isset( $tokens[ $i ]['scope_closer'] )
				&& $tokens[ $i ]['scope_closer'] < $scope_end
			) {
				// Jump over nested arrow functions as long as the current variable isn't *in* the arrow function.
				$i = $tokens[ $i ]['scope_closer'];
				continue;
			}

			if ( isset( self::$targets[ $tokens[ $i ]['code'] ] ) === false ) {
				continue;
			}

			switch ( self::$targets[ $tokens[ $i ]['code'] ] ) {
				case 'construct':
					$issetOpener = $phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), null, true );
					if ( false === $issetOpener
						|| \T_OPEN_PARENTHESIS !== $tokens[ $issetOpener ]['code']
						|| isset( $tokens[ $issetOpener ]['parenthesis_closer'] ) === false
					) {
						// Parse error or live coding.
						continue 2;
					}

					$issetCloser = $tokens[ $issetOpener ]['parenthesis_closer'];

					// Look for this variable. We purposely stomp $i from the parent loop.
					for ( $i = ( $issetOpener + 1 ); $i < $issetCloser; $i++ ) {

						if ( \T_VARIABLE !== $tokens[ $i ]['code'] ) {
							continue;
						}

						if ( $tokens[ $stackPtr ]['content'] !== $tokens[ $i ]['content'] ) {
							continue;
						}

						// If we're checking for specific array keys (ex: 'hello' in
						// $_POST['hello']), that must match too. Quote-style, however, doesn't matter.
						if ( ! empty( $bare_array_keys ) ) {
							$found_keys = VariableHelper::get_array_access_keys( $phpcsFile, $i );
							$found_keys = self::strip_quotes_from_array_values( $found_keys );
							$diff       = array_diff_assoc( $bare_array_keys, $found_keys );
							if ( ! empty( $diff ) ) {
								continue;
							}
						}

						return true;
					}

					break;

				case 'function_call':
					// Only check calls to array_key_exists() and key_exists().
					if ( isset( self::$key_exists_functions[ strtolower( $tokens[ $i ]['content'] ) ] ) === false ) {
						continue 2;
					}

					$next_non_empty = $phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), null, true );
					if ( false === $next_non_empty || \T_OPEN_PARENTHESIS !== $tokens[ $next_non_empty ]['code'] ) {
						// Not a function call.
						continue 2;
					}

					if ( Context::inAttribute( $phpcsFile, $i ) === true ) {
						// Definitely not the function call as those are not allowed in attributes.
						continue 2;
					}

					if ( ContextHelper::has_object_operator_before( $phpcsFile, $i ) === true ) {
						// Method call.
						continue 2;
					}

					if ( ContextHelper::is_token_namespaced( $phpcsFile, $i ) === true ) {
						// Namespaced function call.
						continue 2;
					}

					$params = PassedParameters::getParameters( $phpcsFile, $i );

					// As `key_exists()` is an alias of `array_key_exists()`, the param positions and names are the same.
					$array_param = PassedParameters::getParameterFromStack( $params, 2, 'array' );
					if ( false === $array_param ) {
						continue 2;
					}

					$array_param_first_token = $phpcsFile->findNext( Tokens::$emptyTokens, $array_param['start'], ( $array_param['end'] + 1 ), true );
					if ( false === $array_param_first_token
						|| \T_VARIABLE !== $tokens[ $array_param_first_token ]['code']
						|| $tokens[ $array_param_first_token ]['content'] !== $tokens[ $stackPtr ]['content']
					) {
						continue 2;
					}

					if ( ! empty( $bare_array_keys ) ) {
						// Prevent the original array from being altered.
						$bare_keys = $bare_array_keys;
						$last_key  = array_pop( $bare_keys );

						/*
						 * For multi-level array access, the complete set of keys could be split between
						 * the $key and the $array parameter, but could also be completely in the $array
						 * parameter, so we need to check both options.
						 */
						$found_keys = VariableHelper::get_array_access_keys( $phpcsFile, $array_param_first_token );
						$found_keys = self::strip_quotes_from_array_values( $found_keys );

						// First try matching the complete set against the array parameter.
						$diff = array_diff_assoc( $bare_array_keys, $found_keys );
						if ( empty( $diff ) ) {
							return true;
						}

						// If that failed, try getting an exact match for the subset against the
						// $array parameter and the last key against the first.
						$key_param = PassedParameters::getParameterFromStack( $params, 1, 'key' );
						if ( false !== $key_param
							&& $bare_keys === $found_keys
							&& TextStrings::stripQuotes( $key_param['raw'] ) === $last_key
						) {
							return true;
						}

						// Didn't find the correct array keys.
						continue 2;
					}

					return true;

				case 'coalesce':
					$prev = $i;
					do {
						$prev = $phpcsFile->findPrevious( Tokens::$emptyTokens, ( $prev - 1 ), null, true );
						// Skip over array keys, like `$_GET['key']['subkey']`.
						if ( \T_CLOSE_SQUARE_BRACKET === $tokens[ $prev ]['code'] ) {
							$prev = $tokens[ $prev ]['bracket_opener'];
							continue;
						}

						break;
					} while ( $prev >= ( $scope_start + 1 ) );

					// We should now have reached the variable.
					if ( \T_VARIABLE !== $tokens[ $prev ]['code'] ) {
						continue 2;
					}

					if ( $tokens[ $prev ]['content'] !== $tokens[ $stackPtr ]['content'] ) {
						continue 2;
					}

					if ( ! empty( $bare_array_keys ) ) {
						$found_keys = VariableHelper::get_array_access_keys( $phpcsFile, $prev );
						$found_keys = self::strip_quotes_from_array_values( $found_keys );
						$diff       = array_diff_assoc( $bare_array_keys, $found_keys );
						if ( ! empty( $diff ) ) {
							continue 2;
						}
					}

					// Right variable, correct key.
					return true;
			}
		}

		return false;
	}

	/**
	 * Strip quotes of all the values in an array containing only text strings.
	 *
	 * @since 3.0.0
	 *
	 * @param string[] $text_strings The input array.
	 *
	 * @return string[]
	 */
	private static function strip_quotes_from_array_values( array $text_strings ) {
		return array_map( array( 'PHPCSUtils\Utils\TextStrings', 'stripQuotes' ), $text_strings );
	}
}
