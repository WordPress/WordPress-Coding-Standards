<?php

/**
 * Discourages removal of the admin bar.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_VIP_AdminBarRemovalSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
				T_STRING,
				T_CONSTANT_ENCAPSED_STRING,
				T_DOUBLE_QUOTED_STRING,
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

		if ( in_array( trim( $tokens[$stackPtr]['content'], '"\'' ), array( 'show_admin_bar' ) ) ) {
			$phpcsFile->addError( 'Removal of admin bar is prohibited.', $stackPtr, 'RemovalDetected');
		}
	}//end process()


}//end class

?>
