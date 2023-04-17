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
use PHPCSUtils\Utils\Lists;

/**
 * Helper utilities for checking the context in which a token is used.
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
 * @since   3.0.0 The method in this class was previously contained in the
 *                `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class ListHelper {

	/**
	 * Get a list of the token pointers to the variables being assigned to in a list statement.
	 *
	 * @internal No need to take special measures for nested lists. Nested or not,
	 * each list part can only contain one variable being written to.
	 *
	 * @since 2.2.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method visibility was changed from `protected` to `public static`.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile       The file being scanned.
	 * @param int                         $stackPtr        The position of the T_LIST or T_OPEN_SHORT_ARRAY
	 *                                                     token in the stack.
	 * @param array                       $list_open_close Optional. Array containing the token pointers to
	 *                                                     the list opener and closer.
	 *
	 * @return array Array with the stack pointers to the variables or an empty
	 *               array when not a (short) list.
	 */
	public static function get_list_variables( File $phpcsFile, $stackPtr, $list_open_close = array() ) {
		$tokens = $phpcsFile->getTokens();

		if ( \T_LIST !== $tokens[ $stackPtr ]['code']
			&& \T_OPEN_SHORT_ARRAY !== $tokens[ $stackPtr ]['code']
		) {
			return array();
		}

		if ( empty( $list_open_close ) ) {
			$list_open_close = Lists::getOpenClose( $phpcsFile, $stackPtr );
			if ( false === $list_open_close ) {
				// Not a (short) list.
				return array();
			}
		}

		$var_pointers = array();
		$current      = $list_open_close['opener'];
		$closer       = $list_open_close['closer'];
		$last         = false;
		do {
			++$current;
			$next_comma = $phpcsFile->findNext( \T_COMMA, $current, $closer );
			if ( false === $next_comma ) {
				$next_comma = $closer;
				$last       = true;
			}

			// Skip over the "key" part in keyed lists.
			$arrow = $phpcsFile->findNext( \T_DOUBLE_ARROW, $current, $next_comma );
			if ( false !== $arrow ) {
				$current = ( $arrow + 1 );
			}

			/*
			 * Each list item can only have one variable to which an assignment is being made.
			 * This can be an array with a (variable) index, but that doesn't matter, we're only
			 * concerned with the actual variable.
			 */
			$var = $phpcsFile->findNext( \T_VARIABLE, $current, $next_comma );
			if ( false !== $var ) {
				// Not an empty list item.
				$var_pointers[] = $var;
			}

			$current = $next_comma;

		} while ( false === $last );

		return $var_pointers;
	}
}
