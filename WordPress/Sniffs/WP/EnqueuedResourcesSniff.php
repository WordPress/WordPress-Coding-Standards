<?php
/**
 * WordPress_Sniffs_WP_Enqueued_Resources_Sniff
 *
 * Makes sure scripts and styles are enqueued and not explicitly echo'd
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_WP_EnqueuedResourcesSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CONSTANT_ENCAPSED_STRING,
                T_DOUBLE_QUOTED_STRING,
                T_INLINE_HTML,
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
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $token  = $tokens[$stackPtr];

        if ( preg_match( '#rel=[\'"]?stylesheet[\'"]?#', $token['content'], $matches ) > 0 ) {
            $phpcsFile->addError( 'Stylesheets must be registered/enqueued via wp_enqueue_style', $stackPtr, 'NonEnqueuedStylesheet' );
            return;
        }

        if ( preg_match( '#<script[^>]*(?<=src=)#', $token['content'], $matches ) > 0 ) {
            $phpcsFile->addError( 'Scripts must be registered/enqueued via wp_enqueue_script', $stackPtr, 'NonEnqueuedScript' );
            return;
        }

    }//end process()


}//end class
