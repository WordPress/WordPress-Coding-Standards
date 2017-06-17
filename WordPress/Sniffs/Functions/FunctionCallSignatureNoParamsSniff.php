<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Enforces no whitespace between the parenthesis of a function call without parameters.
 *
 * @link    https://make.wordpress.org/core/handbook/coding-standards/php/#space-usage
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.12.0
 */
class WordPress_Sniffs_Functions_FunctionCallSignatureNoParamsSniff extends WordPress_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return PHP_CodeSniffer_Tokens::$functionNameTokens;
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int Integer stack pointer to skip the rest of the file.
	 */
	public function process_token( $stackPtr ) {

		// Find the next non-empty token.
		$openParenthesis = $this->phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );

		if ( T_OPEN_PARENTHESIS !== $this->tokens[ $openParenthesis ]['code'] ) {
			// Not a function call.
			return;
		}

		if ( ! isset( $this->tokens[ $openParenthesis ]['parenthesis_closer'] ) ) {
			// Not a function call.
			return;
		}

		// Find the previous non-empty token.
		$search   = PHP_CodeSniffer_Tokens::$emptyTokens;
		$search[] = T_BITWISE_AND;
		$previous = $this->phpcsFile->findPrevious( $search, ( $stackPtr - 1 ), null, true );
		if ( T_FUNCTION === $this->tokens[ $previous ]['code'] ) {
			// It's a function definition, not a function call.
			return;
		}

		$closer = $this->tokens[ $openParenthesis ]['parenthesis_closer'];

		if ( ( $closer - 1 ) === $openParenthesis ) {
			return;
		}

		$nextNonWhitespace = $this->phpcsFile->findNext( T_WHITESPACE, ( $openParenthesis + 1 ), null, true );

		if ( $nextNonWhitespace !== $closer ) {
			// Function has params or comment between parenthesis.
			return;
		}

		$fix = $this->phpcsFile->addFixableError(
			'Function calls without parameters should have no spaces between the parenthesis.',
			( $openParenthesis + 1 ),
			'WhitespaceFound'
		);
		if ( true === $fix ) {
			// If there is only whitespace between the parenthesis, it will just be the one token.
			$this->phpcsFile->fixer->replaceToken( ( $openParenthesis + 1 ), '' );
		}

	} // End process_token().

} // End class.
