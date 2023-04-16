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
		$prev = $phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true, null, true );
		if ( false === $prev ) {
			return false;
		}

		$tokens = $phpcsFile->getTokens();
		if ( \T_NS_SEPARATOR !== $tokens[ $prev ]['code'] ) {
			return false;
		}

		$before_prev = $phpcsFile->findPrevious( Tokens::$emptyTokens, ( $prev - 1 ), null, true, null, true );
		if ( false === $before_prev ) {
			return false;
		}

		if ( \T_STRING !== $tokens[ $before_prev ]['code']
			&& \T_NAMESPACE !== $tokens[ $before_prev ]['code']
		) {
			return false;
		}

		return true;
	}
}
