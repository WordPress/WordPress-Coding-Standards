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
class WordPress_Sniffs_PHP_YodaConditionsSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
			T_IF,
			T_ELSEIF,
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

		$openBracket  = $tokens[$stackPtr]['parenthesis_opener'];
		$closeBracket = $tokens[$stackPtr]['parenthesis_closer'];
		$string = '';
		for ($i = ($openBracket + 1); $i < $closeBracket; $i++) {
			$string .= $tokens[$i]['content'];
		}

		preg_match_all( '#((!=|==)=?\s*(true|false|[\'"0-9])\b)#si', $string, $matches );
		foreach ( $matches[0] as $match ) {
			$error = 'Found "' . $match . '". Use Yoda Condition checks, you must';
			$phpcsFile->addError($error, $stackPtr);
		}

	}//end process()


}//end class

?>
