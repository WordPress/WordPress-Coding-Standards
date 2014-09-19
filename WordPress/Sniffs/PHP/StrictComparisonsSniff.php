<?php
/**
 * Enforces Yoda conditional statements , based upon Squiz code
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Matt Robinson
 */

/**
 * Squiz_Sniffs.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   John Godley <john@urbangiraffe.com>
 * @author   Greg Sherwood <gsherwood@squiz.net>
 * @author   Marc McIntyre <mmcintyre@squiz.net>
 */
class WordPress_Sniffs_PHP_StrictComparisonsSniff implements PHP_CodeSniffer_Sniff
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
	 * @param int					$stackPtr  The position of the current token in the
	 *											stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$stackPtr]['code'] === T_IS_EQUAL || $tokens[$stackPtr]['code'] === T_IS_NOT_EQUAL) {
			$error = 'Found: ' . $token['content'] . ' Use strict comparisons (=== or !===)'; //Found "' . $token . '".
			$phpcsFile->addWarning($error, $stackPtr);
		}

	}//end process()


}//end class

?>
