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

		$has_variable = false;
		for ( $i = $stackPtr + 1; $i < $token['bracket_closer']; $i++ ) {
			if ( T_VARIABLE === $tokens[ $i ]['code'] ) {
				$has_variable = true;
				break;
			}
		}

		$spaced1 = ( T_WHITESPACE === $tokens[ $stackPtr + 1 ]['code'] );
		$spaced2 = ( T_WHITESPACE === $tokens[ $token['bracket_closer'] - 1 ]['code'] );

		// If key has ( or is ) a variable, it should have spaces
		// Else, it should NOT have spaces
		if ( $has_variable && ! ( $spaced1 && $spaced2 ) ) {
			$error = 'Array keys should be surrounded by spaces if they contain a variable.';
        	$phpcsFile->addWarning( $error, $stackPtr );
		}
		elseif( ! $has_variable && ( $spaced1 || $spaced2 ) ) {
			$error = 'Array keys should NOT be surrounded by spaces if they do not contain a variable.';
        	$phpcsFile->addWarning( $error, $stackPtr );
		}

	}//end process()

}//end class
