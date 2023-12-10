<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\ObjectOperatorSpacingSniff as Squiz_ObjectOperatorSpacingSniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Ensure there is no whitespace before/after an object operator.
 *
 * Difference with the upstream sniff:
 * - When the `::` operator is used in `::class`, no new line(s) before or after the object operator are allowed.
 *
 * @since 3.0.0
 * @link  https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/src/Standards/Squiz/Sniffs/WhiteSpace/ObjectOperatorSpacingSniff.php
 */
final class ObjectOperatorSpacingSniff extends Squiz_ObjectOperatorSpacingSniff {

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the current token
	 *                                               in the stack passed in $tokens.
	 *
	 * @return void|int Optionally returns a stack pointer. The sniff will not be
	 *                  called again on the current file until the returned stack
	 *                  pointer is reached.
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$tokens            = $phpcsFile->getTokens();
		$property_adjusted = false;

		// Check for `::class` and don't ignore new lines in that case.
		if ( true === $this->ignoreNewlines
			&& \T_DOUBLE_COLON === $tokens[ $stackPtr ]['code']
		) {
			$next_non_empty = $phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );
			if ( \T_STRING === $tokens[ $next_non_empty ]['code']
				&& 'class' === strtolower( $tokens[ $next_non_empty ]['content'] )
			) {
				$property_adjusted    = true;
				$this->ignoreNewlines = false;
			}
		}

		$return = parent::process( $phpcsFile, $stackPtr );

		if ( true === $property_adjusted ) {
			$this->ignoreNewlines = true;
		}

		return $return;
	}
}
