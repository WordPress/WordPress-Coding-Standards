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
use PHPCSUtils\Utils\GetTokensAsString;
use PHPCSUtils\Utils\Parentheses;

/**
 * Helper utilities for working with variables.
 *
 * ---------------------------------------------------------------------------------------------
 * This class is only intended for internal use by WordPressCS and is not part of the public API.
 * This also means that it has no promise of backward compatibility. Use at your own risk.
 * ---------------------------------------------------------------------------------------------
 *
 * {@internal The functionality in this class will likely be replaced at some point in
 * the future by functions from PHPCSUtils.}
 *
 * @internal
 *
 * @since 3.0.0 The methods in this class were previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and have been moved here.
 */
final class VariableHelper {

	/**
	 * Get the index keys of an array variable.
	 *
	 * E.g., "bar" and "baz" in $foo['bar']['baz'].
	 *
	 * @since 2.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - Visibility is now `public` (was `protected`) and the method `static`.
	 *              - The `$phpcsFile` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The index of the variable token in the stack.
	 * @param bool                        $all       Whether to get all keys or only the first.
	 *                                               Defaults to `true`(= all).
	 *
	 * @return array An array of index keys whose value is being accessed.
	 *               or an empty array if this is not array access.
	 */
	public static function get_array_access_keys( File $phpcsFile, $stackPtr, $all = true ) {
		$tokens = $phpcsFile->getTokens();
		$keys   = array();

		if ( isset( $tokens[ $stackPtr ] ) === false
			|| \T_VARIABLE !== $tokens[ $stackPtr ]['code']
		) {
			return $keys;
		}

		$current = $stackPtr;

		do {
			// Find the next non-empty token.
			$open_bracket = $phpcsFile->findNext(
				Tokens::$emptyTokens,
				( $current + 1 ),
				null,
				true
			);

			// If it isn't a bracket, this isn't an array-access.
			if ( false === $open_bracket
				|| \T_OPEN_SQUARE_BRACKET !== $tokens[ $open_bracket ]['code']
				|| ! isset( $tokens[ $open_bracket ]['bracket_closer'] )
			) {
				break;
			}

			$key = GetTokensAsString::compact(
				$phpcsFile,
				( $open_bracket + 1 ),
				( $tokens[ $open_bracket ]['bracket_closer'] - 1 ),
				true
			);

			$keys[]  = trim( $key );
			$current = $tokens[ $open_bracket ]['bracket_closer'];
		} while ( isset( $tokens[ $current ] ) && true === $all );

		return $keys;
	}

	/**
	 * Get the index key of an array variable.
	 *
	 * E.g., "bar" in $foo['bar'].
	 *
	 * @since 0.5.0
	 * @since 2.1.0 Now uses get_array_access_keys() under the hood.
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - Visibility is now `public` (was `protected`) and the method `static`.
	 *              - The `$phpcsFile` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The index of the variable token in the stack.
	 *
	 * @return string|false The array index key whose value is being accessed.
	 */
	public static function get_array_access_key( File $phpcsFile, $stackPtr ) {
		$keys = self::get_array_access_keys( $phpcsFile, $stackPtr, false );
		if ( isset( $keys[0] ) ) {
			return $keys[0];
		}

		return false;
	}

	/**
	 * Check whether a variable is being compared to another value.
	 *
	 * E.g., $var === 'foo', 1 <= $var, etc.
	 *
	 * Also recognizes `switch ( $var )` and `match ( $var )`.
	 *
	 * @since 0.5.0
	 * @since 2.1.0 Added the $include_coalesce parameter.
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - Visibility is now `public` (was `protected`) and the method `static`.
	 *              - The `$phpcsFile` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile        The file being scanned.
	 * @param int                         $stackPtr         The index of this token in the stack.
	 * @param bool                        $include_coalesce Optional. Whether or not to regard the null
	 *                                                      coalesce operator - ?? - as a comparison operator.
	 *                                                      Defaults to true.
	 *                                                      Null coalesce is a special comparison operator in this
	 *                                                      sense as it doesn't compare a variable to whatever is
	 *                                                      on the other side of the comparison operator.
	 *
	 * @return bool Whether this is a comparison.
	 */
	public static function is_comparison( File $phpcsFile, $stackPtr, $include_coalesce = true ) {
		$tokens = $phpcsFile->getTokens();
		if ( isset( $tokens[ $stackPtr ] ) === false ) {
			return false;
		}

		$comparisonTokens = Tokens::$comparisonTokens;
		if ( false === $include_coalesce ) {
			unset( $comparisonTokens[ \T_COALESCE ] );
		}

		// We first check if this is a switch or match statement (switch ( $var )).
		if ( Parentheses::lastOwnerIn( $phpcsFile, $stackPtr, array( \T_SWITCH, \T_MATCH ) ) !== false ) {
			return true;
		}

		// Find the previous non-empty token. We check before the var first because
		// yoda conditions are usually expected.
		$previous_token = $phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true );

		if ( isset( $comparisonTokens[ $tokens[ $previous_token ]['code'] ] ) ) {
			return true;
		}

		// Maybe the comparison operator is after this.
		$next_token = $phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );

		// This might be an opening square bracket in the case of arrays ($var['a']).
		while ( false !== $next_token
			&& \T_OPEN_SQUARE_BRACKET === $tokens[ $next_token ]['code']
			&& isset( $tokens[ $next_token ]['bracket_closer'] )
		) {
			$next_token = $phpcsFile->findNext(
				Tokens::$emptyTokens,
				( $tokens[ $next_token ]['bracket_closer'] + 1 ),
				null,
				true
			);
		}

		if ( false !== $next_token && isset( $comparisonTokens[ $tokens[ $next_token ]['code'] ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if this variable is being assigned a value.
	 *
	 * E.g., $var = 'foo';
	 *
	 * Also handles array assignments to arbitrary depth:
	 *
	 * $array['key'][ $foo ][ something() ] = $bar;
	 *
	 * @since 0.5.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - Visibility is now `public` (was `protected`) and the method `static`.
	 *              - The `$phpcsFile` parameter was added.
	 *              - The `$include_coalesce` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile        The file being scanned.
	 * @param int                         $stackPtr         The index of the token in the stack.
	 *                                                      This must point to either a T_VARIABLE or
	 *                                                      T_CLOSE_SQUARE_BRACKET token.
	 * @param bool                        $include_coalesce Optional. Whether or not to regard the null
	 *                                                      coalesce operator - ?? - as a comparison operator.
	 *                                                      Defaults to true.
	 *                                                      Null coalesce is a special comparison operator in this
	 *                                                      sense as it doesn't compare a variable to whatever is
	 *                                                      on the other side of the comparison operator.
	 *
	 * @return bool Whether the token is a variable being assigned a value.
	 */
	public static function is_assignment( File $phpcsFile, $stackPtr, $include_coalesce = true ) {
		$tokens = $phpcsFile->getTokens();
		if ( isset( $tokens[ $stackPtr ] ) === false ) {
			return false;
		}

		static $valid = array(
			\T_VARIABLE             => true,
			\T_CLOSE_SQUARE_BRACKET => true,
		);

		// Must be a variable or closing square bracket (see below).
		if ( ! isset( $valid[ $tokens[ $stackPtr ]['code'] ] ) ) {
			return false;
		}

		$next_non_empty = $phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true, null, true );

		// No token found.
		if ( false === $next_non_empty ) {
			return false;
		}

		$assignmentTokens = Tokens::$assignmentTokens;
		if ( false === $include_coalesce ) {
			unset( $assignmentTokens[ \T_COALESCE_EQUAL ] );
		}

		// If the next token is an assignment, that's all we need to know.
		if ( isset( $assignmentTokens[ $tokens[ $next_non_empty ]['code'] ] ) ) {
			return true;
		}

		// Check if this is an array assignment, e.g., `$var['key'] = 'val';` .
		if ( \T_OPEN_SQUARE_BRACKET === $tokens[ $next_non_empty ]['code']
			&& isset( $tokens[ $next_non_empty ]['bracket_closer'] )
		) {
			return self::is_assignment( $phpcsFile, $tokens[ $next_non_empty ]['bracket_closer'], $include_coalesce );
		}

		return false;
	}
}
