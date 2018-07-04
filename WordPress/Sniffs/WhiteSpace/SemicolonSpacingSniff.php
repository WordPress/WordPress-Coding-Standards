<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\WhiteSpace;

use Squiz_Sniffs_WhiteSpace_SemicolonSpacingSniff as PHPCS_Squiz_SemicolonSpacingSniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Ensure there is no whitespace before a semicolon, while allowing for empty conditions in a `for`.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 */
class SemicolonSpacingSniff extends PHPCS_Squiz_SemicolonSpacingSniff {

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the current token
	 *                                               in the stack passed in $tokens.
	 *
	 * @return void|int
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		// Don't examine semi-colons for empty conditions in `for()` control structures.
		if ( isset( $tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			$nested_parenthesis = $tokens[ $stackPtr ]['nested_parenthesis'];
			$close_parenthesis  = end( $nested_parenthesis );

			if ( isset( $tokens[ $close_parenthesis ]['parenthesis_owner'] ) ) {
				$owner = $tokens[ $close_parenthesis ]['parenthesis_owner'];

				if ( \T_FOR === $tokens[ $owner ]['code'] ) {
					$previous = $phpcsFile->findPrevious(
						Tokens::$emptyTokens,
						( $stackPtr - 1 ),
						$tokens[ $owner ]['parenthesis_opener'],
						true
					);

					if ( false !== $previous
						&& ( $previous === $tokens[ $owner ]['parenthesis_opener']
							|| \T_SEMICOLON === $tokens[ $previous ]['code'] )
					) {
						return;
					}
				}
			}
		}

		return parent::process( $phpcsFile, $stackPtr );
	}

}
