<?php
/**
 * Disallow omission of braces for single line blocks
 * 
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://make.wordpress.org/core/2013/11/13/proposed-coding-standards-change-always-require-braces/
 * @author   Shady Sharaf <shady@x-team.com>
 */

/**
 * Disallow omission of braces for single line blocks
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_Functions_FunctionBracesSniff implements PHP_CodeSniffer_Sniff
{


	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
			T_OPEN_PARENTHESIS
			);

	}//end register()


	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token in the
	 *                                        stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr )
	{
		$tokens = $phpcsFile->getTokens();

		$functionPtr = $phpcsFile->findPrevious( array( T_WHITESPACE ), $stackPtr - 1, null, true );
		
		// If not a function call, return
		$blocks = array(
			T_WHILE,
			T_FOR,
			T_FOREACH,
			T_IF,
			T_ELSE,
			T_ELSEIF,
			T_WHILE,
			T_DO,
			T_TRY,
			T_CATCH,
			T_SWITCH,
			);
		if ( ! in_array( $tokens[$functionPtr]['code'], $blocks ) ) {
			return;
		}

		$nextToken = $phpcsFile->findNext( array( T_WHITESPACE, T_COMMENT ), $stackPtr + 1, null, true );

		if ( ! in_array( $tokens[$nextToken]['code'], array( T_OPEN_CURLY_BRACKET ) ) ) {
			$phpcsFile->addError( 'Single line blocks should always use braces.', $nextToken );
		}

	}//end process()


}//end class

?>
