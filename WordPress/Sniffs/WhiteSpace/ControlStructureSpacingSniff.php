<?php
/**
 * Enforces spacing around logical operators and assignments, based upon Squiz code
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   John Godley <john@urbangiraffe.com>
 * @author   Greg Sherwood <gsherwood@squiz.net>
 * @author   Marc McIntyre <mmcintyre@squiz.net>
 */

/**
 * Squiz_Sniffs_WhiteSpace_ControlStructureSpacingSniff.
 *
 * Checks that any array declarations are lower case.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   John Godley <john@urbangiraffe.com>
 * @author   Greg Sherwood <gsherwood@squiz.net>
 * @author   Marc McIntyre <mmcintyre@squiz.net>
 */
class WordPress_Sniffs_WhiteSpace_ControlStructureSpacingSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array( 'PHP' );

    /**
     * Check for blank lines on start/end of control structures
     * @var boolean
     */
    public $blank_line_check = false;

	/**
	 * Check for blank lines after control structures.
	 *
	 * @var boolean
	 */
	public $blank_line_after_check = true;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_IF,
                T_WHILE,
                T_FOREACH,
                T_FOR,
                T_SWITCH,
                T_DO,
                T_ELSE,
                T_ELSEIF,
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
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            $error = 'Space after opening control structure is required';
            $phpcsFile->addError($error, $stackPtr);
        }

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        $scopeCloser = $tokens[$stackPtr]['scope_closer'];
        $scopeOpener = $tokens[$stackPtr]['scope_opener'];

        $openBracket = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if (($stackPtr + 1) === $openBracket) {
            // Checking this: $value = my_function[*](...).
            $error = 'No space before opening parenthesis is prohibited';
            $phpcsFile->addError($error, $stackPtr);
        }

        if ($tokens[($openBracket + 1)]['code'] !== T_WHITESPACE && $tokens[($openBracket + 1)]['code'] !== T_CLOSE_PARENTHESIS) {
            // Checking this: $value = my_function([*]...).
            $error = 'No space after opening parenthesis is prohibited';
            $phpcsFile->addError($error, $stackPtr);
        }

        if (isset($tokens[$openBracket]['parenthesis_closer']) === true) {
            $closer = $tokens[$openBracket]['parenthesis_closer'];

            if ($tokens[($closer - 1)]['code'] !== T_WHITESPACE) {
                $error = 'No space before closing parenthesis is prohibited';
                $phpcsFile->addError($error, $closer);
            }

            if (isset($tokens[$openBracket]['parenthesis_owner']) && $tokens[$closer]['line'] !== $tokens[$scopeOpener]['line']) {
                $error = 'Opening brace should be on the same line as the declaration';
                $phpcsFile->addError($error, $openBracket);
                return;
            }
        }

        if ( $this->blank_line_check ) {
            $firstContent = $phpcsFile->findNext(T_WHITESPACE, ($scopeOpener + 1), null, true);
            if ($tokens[$firstContent]['line'] !== ($tokens[$scopeOpener]['line'] + 1) && ! in_array($tokens[$firstContent]['code'], array(T_CLOSE_TAG, T_COMMENT))) {
                $error = 'Blank line found at start of control structure';
                $phpcsFile->addError($error, $scopeOpener);
            }

            $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($scopeCloser - 1), null, true);
            if ($tokens[$lastContent]['line'] !== ($tokens[$scopeCloser]['line'] - 1)) {
                $errorToken = $scopeCloser;
                for ($i = ($scopeCloser - 1); $i > $lastContent; $i--) {
                    if ($tokens[$i]['line'] < $tokens[$scopeCloser]['line'] && $tokens[$firstContent]['code'] !== T_OPEN_TAG) {
                        $error = 'Blank line found at end of control structure';
                        $phpcsFile->addError($error, $i);
                        break;
                    }
                }
            }
        }

        $trailingContent = $phpcsFile->findNext(T_WHITESPACE, ($scopeCloser + 1), null, true);
        if ($tokens[$trailingContent]['code'] === T_ELSE) {
            if ($tokens[$stackPtr]['code'] === T_IF) {
                // IF with ELSE.
                return;
            }
        }

        if ($tokens[$trailingContent]['code'] === T_COMMENT) {
            if ($tokens[$trailingContent]['line'] === $tokens[$scopeCloser]['line']) {
                if (substr($tokens[$trailingContent]['content'], 0, 5) === '//end') {
                    // There is an end comment, so we have to get the next piece
                    // of content.
                    $trailingContent = $phpcsFile->findNext(T_WHITESPACE, ($trailingContent + 1), null, true);
                }
            }
        }

        if ($tokens[$trailingContent]['code'] === T_BREAK) {
            // If this BREAK is closing a CASE, we don't need the
            // blank line after this control structure.
            if (isset($tokens[$trailingContent]['scope_condition']) === true) {
                $condition = $tokens[$trailingContent]['scope_condition'];
                if ($tokens[$condition]['code'] === T_CASE || $tokens[$condition]['code'] === T_DEFAULT) {
                    return;
                }
            }
        }

        if ($tokens[$trailingContent]['code'] === T_CLOSE_TAG) {
            // At the end of the script or embedded code.
            return;
        }

        if ($tokens[$trailingContent]['code'] === T_CLOSE_CURLY_BRACKET) {
            // Another control structure's closing brace.
            if (isset($tokens[$trailingContent]['scope_condition']) === true) {
                $owner = $tokens[$trailingContent]['scope_condition'];
                if ($tokens[$owner]['code'] === T_FUNCTION) {
                    // The next content is the closing brace of a function
                    // so normal function rules apply and we can ignore it.
                    return;
                }
            }

            if ($this->blank_line_after_check && $tokens[$trailingContent]['line'] !== ($tokens[$scopeCloser]['line'] + 1)) {
                $error = 'Blank line found after control structure';
                $phpcsFile->addError($error, $scopeCloser);
            }
        }

    }//end process()


}//end class

?>
