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

/**
 * Helper utilities for working with variables representing arrays.
 *
 * ---------------------------------------------------------------------------------------------
 * This class is only intended for internal use by WordPressCS and is not part of the public API.
 * This also means that it has no promise of backward compatibility. Use at your own risk.
 * ---------------------------------------------------------------------------------------------
 *
 * {@internal The functionality in this class will likely be replaced at some point in
 * the future by functions from PHPCSUtils.}
 *
 * @package WPCS\WordPressCodingStandards
 * @since   3.0.0 The methods in this class were previously contained in the
 *                `WordPressCS\WordPress\Sniff` class and have been moved here.
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
	 *              - The $phpcsFile parameter was added.
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

		if ( \T_VARIABLE !== $tokens[ $stackPtr ]['code'] ) {
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

			$key = $phpcsFile->getTokensAsString(
				( $open_bracket + 1 ),
				( $tokens[ $open_bracket ]['bracket_closer'] - $open_bracket - 1 )
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
	 *              - The $phpcsFile parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The index of the token in the stack.
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
}
