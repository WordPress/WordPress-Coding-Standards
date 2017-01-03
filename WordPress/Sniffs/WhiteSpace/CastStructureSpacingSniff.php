<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Ensure cast statements don't contain whitespace, but *are* surrounded by whitespace, based upon Squiz code.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#space-usage
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.11.0 This sniff now has the ability to fix the issues it flags.
 * @since   0.11.0 The error level for all errors thrown by this sniff has been raised from warning to error.
 */
class WordPress_Sniffs_WhiteSpace_CastStructureSpacingSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return PHP_CodeSniffer_Tokens::$castTokens;
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token in the
	 *                                        stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		if ( T_WHITESPACE !== $tokens[ ( $stackPtr - 1 ) ]['code'] ) {
			$error = 'No space before opening casting parenthesis is prohibited';
			$fix   = $phpcsFile->addFixableError( $error, $stackPtr, 'NoSpaceBeforeOpenParenthesis' );
			if ( true === $fix ) {
				$phpcsFile->fixer->addContentBefore( $stackPtr, ' ' );
			}
		}

		if ( T_WHITESPACE !== $tokens[ ( $stackPtr + 1 ) ]['code'] ) {
			$error = 'No space after closing casting parenthesis is prohibited';
			$fix   = $phpcsFile->addFixableError( $error, $stackPtr, 'NoSpaceAfterCloseParenthesis' );
			if ( true === $fix ) {
				$phpcsFile->fixer->addContent( $stackPtr, ' ' );
			}
		}
	}

} // End class.
