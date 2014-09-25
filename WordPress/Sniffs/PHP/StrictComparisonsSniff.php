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

		if ( $tokens[$stackPtr]['code'] !== T_IS_EQUAL && $tokens[$stackPtr]['code'] !== T_IS_NOT_EQUAL) {
        	return;
    	} else {
			if ( ! $this->has_whitelist_comment( 'loose comparison okay', $tokens, $stackPtr ) ) {
        		$error = 'Found: ' . $tokens[$stackPtr]['content'] . '. Use strict comparisons (=== or !==).';
				$phpcsFile->addWarning($error, $stackPtr);
    		}
    	}

	}//end process()

	function has_whitelist_comment( $comment, $tokens, $stackPtr ) {
        // get tokens, get the last token in the line,
        // check if it's a comment and matches $comment, return true or false
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
			preg_match( '#' . $comment . '#i', $tokens[$nextPtr]['content'] ) > 0
		);

		if ( ! $is_whitelisted ) {
			return false;
		} else {
			return true;
		}
	}

}//end class

?>
