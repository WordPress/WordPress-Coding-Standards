<?php
/**
 * WordPress Coding Standard.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * WordPress_Sniffs_VIP_SessionVariableUsageSniff
 *
 * Discourages the use of the session variable.
 * Creating a session writes a file to the server and is unreliable in a multi-server environment.
 *
 * @link     https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/75
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_VIP_SessionVariableUsageSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_VARIABLE,
		);

	} // end register()

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @todo Allow T_CONSTANT_ENCAPSED_STRING?
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		if ( '$_SESSION' === $tokens[ $stackPtr ]['content'] ) {
			$phpcsFile->addError( 'Usage of $_SESSION variable is prohibited.', $stackPtr, 'SessionVarsProhibited' );
		}

	} // end process()

} // end class
