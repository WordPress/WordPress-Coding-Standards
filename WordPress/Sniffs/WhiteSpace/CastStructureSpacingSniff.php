<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Util\Tokens;
use WordPressCS\WordPress\Sniff;

/**
 * Ensure cast statements are preceded by whitespace.
 *
 * @link https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/#space-usage
 *
 * @since 0.3.0
 * @since 0.11.0 This sniff now has the ability to fix the issues it flags.
 * @since 0.11.0 The error level for all errors thrown by this sniff has been raised from warning to error.
 * @since 0.12.0 This class now extends the WordPressCS native `Sniff` class.
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 1.2.0  Removed the `NoSpaceAfterCloseParenthesis` error code in favour of the
 *               upstream `Generic.Formatting.SpaceAfterCast.NoSpace` error.
 * @since 2.2.0  Added exception for whitespace between spread operator and cast.
 */
final class CastStructureSpacingSniff extends Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return Tokens::$castTokens;
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {

		if ( \T_WHITESPACE !== $this->tokens[ ( $stackPtr - 1 ) ]['code']
			&& \T_ELLIPSIS !== $this->tokens[ ( $stackPtr - 1 ) ]['code']
		) {
			$error = 'Expected a space before the type cast open parenthesis; none found';
			$fix   = $this->phpcsFile->addFixableError( $error, $stackPtr, 'NoSpaceBeforeOpenParenthesis' );
			if ( true === $fix ) {
				$this->phpcsFile->fixer->addContentBefore( $stackPtr, ' ' );
			}
		}
	}
}
