<?php
/**
 * WordPress_Sniffs_WhiteSpace_PhpIndentSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @author    Weston Ruter <weston@x-team.com>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class WordPress_Sniffs_WhiteSpace_PhpIndentSniff implements PHP_CodeSniffer_Sniff
{

    public $supportedTokenizers = array( 'PHP' );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$emptyTokens;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['column'] === 1) {
            $lineContent = '';
            $tokensCount = count($tokens);
            for ($i = 0; ($stackPtr + $i) < $tokensCount; $i++) {
                $tokenContent = $tokens[($stackPtr + $i)]['content'];
                $lineContent .= $tokenContent;

                if ($tokens[($stackPtr + $i)]['code'] !== T_WHITESPACE) {
                    break;
                }

                $spacePos = strpos($tokenContent, ' ');
                if ($spacePos !== false) {
                    $error = 'Line is indented with space not tab';
                    $phpcsFile->addWarning($error, ($stackPtr + $i), 'Incorrect', $tokenContent.'<');
                    break;
                }
            }
        }

    }//end process()


}//end class

?>
