<?php
/**
 * Flag any usage of super global input var ( _GET / _POST / etc. )
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 * @link     https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/79
 */
class WordPress_Sniffs_VIP_SuperGlobalInputUsageSniff extends WordPress_Sniff
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
		$this->init( $phpcsFile );
		$tokens = $phpcsFile->getTokens();

		// Check for global input variable
		if ( ! in_array( $tokens[ $stackPtr ]['content'], WordPress_Sniff::$input_superglobals ) ) {
			return;
		}

		$varName = $tokens[$stackPtr]['content'];

		// If we're overriding a superglobal with an assignment, no need to test
		$semicolon_position = $phpcsFile->findNext( array( T_SEMICOLON ), $stackPtr + 1, null, null, null, true );
		$assignment_position = $phpcsFile->findNext( array( T_EQUAL ), $stackPtr + 1, null, null, null, true );
		if ( $semicolon_position !== false && $assignment_position !== false && $assignment_position < $semicolon_position ) {
			return;
		}

		// Check for whitelisting comment
		if ( ! $this->has_whitelist_comment( 'input var', $stackPtr ) ) {
			$phpcsFile->addWarning( 'Detected access of super global var %s, probably need manual inspection.', $stackPtr, 'AccessDetected', array( $varName ) );
		}
	}//end process()

}//end class
