<?php
/**
 * Flag calling in_array() without true as the third parameter.
 *
 * @link https://vip.wordpress.com/documentation/code-review-what-we-look-for/#using-in_array-without-strict-parameter
 * @category PHP
 * @package  PHP_CodeSniffer
 */
class WordPress_Sniffs_PHP_StrictInArraySniff extends WordPress_Sniffs_Arrays_ArrayAssignmentRestrictionsSniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_STRING
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		// Skip any token that is not 'in_array'.
		if ( 'in_array' !== strtolower( $tokens[ $stackPtr ]['content'] ) ) {
			return;
		}

		if ( ! isset( $tokens[ ( $stackPtr - 1 ) ] ) ) {
			return;
		}

		$prevToken = $phpcsFile->findPrevious( array( T_WHITESPACE, T_COMMENT ), ( $stackPtr - 1 ), null, true );

		// Skip if this is instance of in_array() not a function call.
		if ( false === $prevToken || in_array( $tokens[ $prevToken ]['code'], array( T_OBJECT_OPERATOR, T_DOUBLE_COLON ), true ) ) {
			return;
		}

		// Get the closing parenthesis.
		$openParenthesis = $phpcsFile->findNext( T_OPEN_PARENTHESIS, ( $stackPtr + 1 ) );
		if ( false === $openParenthesis ) {
			return;
		}

		// Gracefully handle syntax error.
		if ( ! isset( $tokens[ $openParenthesis ]['parenthesis_closer'] ) ) {
			$phpcsFile->addError( 'Missing closing parenthesis for in_array().', $openParenthesis, 'MissingClosingParenthesis' );
			return;
		}

		// Get last token in the function call.
		$closeParenthesis = $tokens[ $openParenthesis ]['parenthesis_closer'];
		$lastToken        = $phpcsFile->findPrevious( array( T_WHITESPACE, T_COMMENT ), ( $closeParenthesis - 1 ), ( $openParenthesis + 1 ), true );
		if ( false === $lastToken ) {
			$phpcsFile->addError( 'Missing arguments to in_array().', $openParenthesis, 'MissingArguments' );
			return;
		}

		if ( T_TRUE !== $tokens[ $lastToken ]['code'] ) {
			$phpcsFile->addWarning( 'Not using strict comparison for in_array(); supply true for third argument.', $lastToken, 'MissingTrueStrict' );
			return;
		}
	} // end process()

} // end class
