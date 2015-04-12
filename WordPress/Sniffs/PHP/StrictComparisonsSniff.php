<?php
/**
 * Enforces Strict Comparison checks, based upon Squiz code
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Matt Robinson
 */

class WordPress_Sniffs_PHP_StrictComparisonsSniff extends WordPress_Sniff
{

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
			T_IS_EQUAL,
			T_IS_NOT_EQUAL,
		);

	}//end register()


	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File	$phpcsFile The file being scanned.
	 * @param int			$stackPtr  The position of the current token in the
	 *						stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$this->init( $phpcsFile );

		if ( ! $this->has_whitelist_comment( 'loose comparison', $stackPtr ) ) {
			$tokens = $phpcsFile->getTokens();
			$error = 'Found: ' . $tokens[$stackPtr]['content'] . '. Use strict comparisons (=== or !==).';
			$phpcsFile->addWarning($error, $stackPtr);
		}

	}//end process()

}//end class
