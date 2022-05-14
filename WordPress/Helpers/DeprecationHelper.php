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
 * Helper utilities for checking whether something has been marked as deprecated.
 *
 * {@internal The functionality in this class will likely be replaced at some point in
 * the future by functions from PHPCSUtils.}
 *
 * @package WPCS\WordPressCodingStandards
 * @since   3.0.0 The method in this class was previously contained in the
 *                `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class DeprecationHelper {

	/**
	 * Check whether a function has been marked as deprecated via a @deprecated tag
	 * in the function docblock.
	 *
	 * @since 2.2.0
	 * @since 3.0.0 Moved from the Sniff class to this class.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of a T_FUNCTION
	 *                                               token in the stack.
	 *
	 * @return bool
	 */
	public static function is_function_deprecated( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$find   = Tokens::$methodPrefixes;
		$find[] = \T_WHITESPACE;

		$comment_end = $phpcsFile->findPrevious( $find, ( $stackPtr - 1 ), null, true );
		if ( \T_DOC_COMMENT_CLOSE_TAG !== $tokens[ $comment_end ]['code'] ) {
			// Function doesn't have a doc comment or is using the wrong type of comment.
			return false;
		}

		$comment_start = $tokens[ $comment_end ]['comment_opener'];
		foreach ( $tokens[ $comment_start ]['comment_tags'] as $tag ) {
			if ( '@deprecated' === $tokens[ $tag ]['content'] ) {
				return true;
			}
		}

		return false;
	}
}
