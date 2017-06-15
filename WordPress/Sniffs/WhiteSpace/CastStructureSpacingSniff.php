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
 * @since   0.12.0 This class now extends WordPress_Sniff.
 */
class WordPress_Sniffs_WhiteSpace_CastStructureSpacingSniff extends WordPress_Sniff {

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
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {

		if ( T_WHITESPACE !== $this->tokens[ ( $stackPtr - 1 ) ]['code'] ) {
			$error = 'No space before opening casting parenthesis is prohibited';
			$fix   = $this->phpcsFile->addFixableError( $error, $stackPtr, 'NoSpaceBeforeOpenParenthesis' );
			if ( true === $fix ) {
				$this->phpcsFile->fixer->addContentBefore( $stackPtr, ' ' );
			}
		}

		if ( T_WHITESPACE !== $this->tokens[ ( $stackPtr + 1 ) ]['code'] ) {
			$error = 'No space after closing casting parenthesis is prohibited';
			$fix   = $this->phpcsFile->addFixableError( $error, $stackPtr, 'NoSpaceAfterCloseParenthesis' );
			if ( true === $fix ) {
				$this->phpcsFile->fixer->addContent( $stackPtr, ' ' );
			}
		}
	}

} // End class.
