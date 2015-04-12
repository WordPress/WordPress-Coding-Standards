<?php
/**
 * Check for proper spacing in array key references
 *
 * @see  http://make.wordpress.org/core/handbook/coding-standards/php/#space-usage
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_Arrays_ArrayKeySpacingRestrictionsSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
				T_OPEN_SQUARE_BRACKET,
			   );

	}//end register()

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr )
	{
		$tokens = $phpcsFile->getTokens();

		$token = $tokens[ $stackPtr ];
		if ( ! isset( $token['bracket_closer'] ) ) {
			$phpcsFile->addWarning( 'Missing bracket closer.', $stackPtr, 'MissingBracketCloser' );
			return;
		}

		$need_spaces = $phpcsFile->findNext(
			array( T_CONSTANT_ENCAPSED_STRING, T_LNUMBER, T_WHITESPACE, T_MINUS ),
			$stackPtr + 1,
			$token['bracket_closer'],
			true
		);

		$spaced1 = ( T_WHITESPACE === $tokens[ $stackPtr + 1 ]['code'] );
		$spaced2 = ( T_WHITESPACE === $tokens[ $token['bracket_closer'] - 1 ]['code'] );

		// It should have spaces only if it only has strings or numbers as the key
		if ( $need_spaces && ! ( $spaced1 && $spaced2 ) ) {
			$error = 'Array keys must be surrounded by spaces unless they contain a string or an integer.';
        	$phpcsFile->addError( $error, $stackPtr, 'NoSpacesAroundArrayKeys' );
		}
		elseif( ! $need_spaces && ( $spaced1 || $spaced2 ) ) {
			$error = 'Array keys must NOT be surrounded by spaces if they only contain a string or an integer.';
        	$phpcsFile->addError( $error, $stackPtr, 'SpacesAroundArrayKeys' );
		}

	}//end process()

}//end class
