<?php
/**
 * Flag any usage of super global input var ( _GET / _POST / _REQUEST )
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 * @link     https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/79
 */
class WordPress_Sniffs_VIP_SuperGlobalInputUsageSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
				T_VARIABLE,
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

		// Check for global input variable
		if ( ! in_array( $tokens[$stackPtr]['content'], array( '$_GET', '$_POST', '$_REQUEST' ) ) )
			return;

		$varName = $tokens[$stackPtr]['content'];

		// If we're overriding a superglobal with an assignment, no need to test
		$semicolon_position = $phpcsFile->findNext( array( T_SEMICOLON ), $stackPtr + 1, null, null, null, true );
		$assignment_position = $phpcsFile->findNext( array( T_EQUAL ), $stackPtr + 1, null, null, null, true );
		if ( $semicolon_position !== false && $assignment_position !== false && $assignment_position < $semicolon_position ) {
			return;
		}

		// Check for whitelisting comment
		$currentLine = $tokens[$stackPtr]['line'];
		$nextPtr = $stackPtr;
		while ( isset( $tokens[$nextPtr + 1]['line'] ) && $tokens[$nextPtr + 1]['line'] == $currentLine ) {
			$nextPtr++;
			// Do nothing, we just want the last token of the line
		}

		$is_whitelisted = ( 
			$tokens[$nextPtr]['code'] === T_COMMENT 
			&& 
			preg_match( '#input var okay#i', $tokens[$nextPtr]['content'] ) > 0
			);

		if ( ! $is_whitelisted ) {
			$phpcsFile->addWarning( 'Detected access of super global var %s, probably need manual inspection.', $stackPtr, null, array( $varName ) );
		}
	}//end process()

}//end class
