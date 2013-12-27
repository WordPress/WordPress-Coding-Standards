<?php
/**
 * WordPress_Sniffs_VIP_SessionVariableUsageSniff
 *
 * Discourages the use of the session variable
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 * @link     https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/69
 */
class WordPress_Sniffs_VIP_SessionVariableUsageSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff
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
     * @todo Allow T_CONSTANT_ENCAPSED_STRING?
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ( $tokens[$stackPtr]['content'] == '$_SESSION' ) {
        	$phpcsFile->addError('Usage of $_SESSION variable is prohibited.', $stackPtr);
        }


    }//end process()



}//end class
