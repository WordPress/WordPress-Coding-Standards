<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHPCSUtils\Tokens\Collections;
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
 * @internal
 *
 * @since 3.0.0 The method in this class was previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class ListHelper {

	/**
	 * Get a list of the token pointers to the variables being assigned to in a list statement.
	 *
	 * {@internal No need to take special measures for nested lists. Nested or not,
	 * each list part can only contain one variable being written to.}
	 *
	 * @since 2.2.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method visibility was changed from `protected` to `public static`.
	 *              - The `$phpcsFile` parameter was added.
	 *              - The `$list_open_close` parameter was dropped.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the T_LIST or T_OPEN_SHORT_ARRAY
	 *                                               token in the stack.
	 *
	 * @return array Array with the stack pointers to the variables or an empty
	 *               array when not a (short) list.
	 */
	public static function get_list_variables( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		// Is this one of the tokens this function handles ?
		if ( isset( $tokens[ $stackPtr ], Collections::listOpenTokensBC()[ $tokens[ $stackPtr ]['code'] ] ) === false ) {
			return array();
		}

		if ( isset( Collections::shortArrayListOpenTokensBC()[ $tokens[ $stackPtr ]['code'] ] )
			&& Lists::isShortList( $phpcsFile, $stackPtr ) === false
		) {
			return array();
		}

		try {
			$assignments = Lists::getAssignments( $phpcsFile, $stackPtr );
		} catch ( RuntimeException $e ) {
			// Parse error/live coding.
			return array();
		}

		$var_pointers = array();

		foreach ( $assignments as $assign ) {
			if ( true === $assign['is_empty'] ) {
				continue;
			}

			if ( true === $assign['is_nested_list'] ) {
				/*
				 * Recurse into the nested list and get the variables.
				 * No need to `catch` any errors as only lists can be nested in lists.
				 */
				$var_pointers += self::get_list_variables( $phpcsFile, $assign['assignment_token'] );
				continue;
			}

			/*
			 * Ok, so this must be a "normal" assignment in the list.
			 * Set the variable pointer both as the key as well as the value, so we can use array join
			 * for nested lists (above).
			 */
			$var_pointers[ $assign['assignment_token'] ] = $assign['assignment_token'];
		}

		return $var_pointers;
	}
}
