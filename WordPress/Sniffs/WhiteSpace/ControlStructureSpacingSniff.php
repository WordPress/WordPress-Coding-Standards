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
     * Require for space before T_COLON when using the alternative syntax for control structures
     *
     * @var string one of 'required', 'forbidden', optional'
     */
    public $space_before_colon = 'required';


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
        $this->blank_line_check       = (bool) $this->blank_line_check;
        $this->blank_line_after_check = (bool) $this->blank_line_after_check;

        $tokens = $phpcsFile->getTokens();

        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE
            && ! ( $tokens[$stackPtr]['code'] === T_ELSE && $tokens[($stackPtr + 1)]['code'] === T_COLON )
        ) {
            $error = 'Space after opening control structure is required';
            if (isset($phpcsFile->fixer) === true) {
                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfterStructureOpen');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->addContent($stackPtr, ' ');
                    $phpcsFile->fixer->endChangeset();
                }
            } else {
                $phpcsFile->addError($error, $stackPtr, 'NoSpaceAfterStructureOpen');
            }
        }

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        $scopeOpener = $tokens[$stackPtr]['scope_opener'];
        $scopeCloser = $tokens[$stackPtr]['scope_closer'];

        // alternative syntax
        if ( $tokens[$scopeOpener]['code'] === T_COLON ) {

            if ( $this->space_before_colon === 'required') {

                if ( $tokens[$scopeOpener - 1]['code'] !== T_WHITESPACE ) {
                   $error = 'Space between opening control structure and T_COLON is required';

                   if ( isset($phpcsFile->fixer) === true ) {
                       $fix = $phpcsFile->addFixableError($error, $scopeOpener, 'NoSpaceBetweenStructureColon');

                       if ($fix === true) {
                           $phpcsFile->fixer->beginChangeset();
                           $phpcsFile->fixer->addContentBefore($scopeOpener, ' ');
                           $phpcsFile->fixer->endChangeset();
                       }
                   } else {
                       $phpcsFile->addError($error, $stackPtr, 'NoSpaceBetweenStructureColon');
                   }
                }

            } elseif ( $this->space_before_colon === 'forbidden' ) {

                if ( $tokens[$scopeOpener - 1]['code'] === T_WHITESPACE ) {
                    $error = 'Extra space between opening control structure and T_COLON found';

                    if ( isset($phpcsFile->fixer) === true ) {
                        $fix = $phpcsFile->addFixableError( $error, $scopeOpener - 1, 'SpaceBetweenStructureColon' );

                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->replaceToken( $scopeOpener - 1, '' );
                            $phpcsFile->fixer->endChangeset();
                        }
                    } else {
                        $phpcsFile->addError( $error, $stackPtr, 'SpaceBetweenStructureColon' );
                    }
                }
            }
        }

        $parenthesisOpener = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if (($stackPtr + 1) === $parenthesisOpener && $tokens[$parenthesisOpener]['code'] !== T_COLON) {
            // Checking this: $value = my_function[*](...).
            $error = 'No space before opening parenthesis is prohibited';
            if (isset($phpcsFile->fixer) === true) {
                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBeforeOpenParenthesis');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->addContent($stackPtr, ' ');
                    $phpcsFile->fixer->endChangeset();
                }
            } else {
                $phpcsFile->addError($error, $stackPtr, 'NoSpaceBeforeOpenParenthesis');
            }
        }

        if ($tokens[($parenthesisOpener + 1)]['code'] !== T_WHITESPACE
            && $tokens[($parenthesisOpener + 1)]['code'] !== T_CLOSE_PARENTHESIS
        ) {
            // Checking this: $value = my_function([*]...).
            $error = 'No space after opening parenthesis is prohibited';
            if (isset($phpcsFile->fixer) === true) {
                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfterOpenParenthesis');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->addContent($parenthesisOpener, ' ');
                    $phpcsFile->fixer->endChangeset();
                }
            } else {
                $phpcsFile->addError($error, $stackPtr, 'NoSpaceAfterOpenParenthesis');
            }
        }

        if (isset($tokens[$parenthesisOpener]['parenthesis_closer']) === true) {
            $parenthesisCloser = $tokens[$parenthesisOpener]['parenthesis_closer'];

            if ($tokens[($parenthesisCloser - 1)]['code'] !== T_WHITESPACE) {
                $error = 'No space before closing parenthesis is prohibited';
                if (isset($phpcsFile->fixer) === true) {
                    $fix = $phpcsFile->addFixableError($error, $parenthesisCloser, 'NoSpaceBeforeCloseParenthesis');
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        $phpcsFile->fixer->addContentBefore($parenthesisCloser, ' ');
                        $phpcsFile->fixer->endChangeset();
                    }
                } else {
                    $phpcsFile->addError($error, $parenthesisCloser, 'NoSpaceBeforeCloseParenthesis');
                }
            }

            if (isset($tokens[$parenthesisOpener]['parenthesis_owner']) === true
                && $tokens[$parenthesisCloser]['line'] !== $tokens[$scopeOpener]['line']
            ) {
                $error = 'Opening brace should be on the same line as the declaration';
                if (isset($phpcsFile->fixer) === true) {
                    $fix = $phpcsFile->addFixableError($error, $parenthesisOpener, 'OpenBraceNotSameLine');
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();

                        for ($i = ($parenthesisCloser + 1); $i < $scopeOpener; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        // TODO: Should be a separate check for spacing between ")" and "{" only (when they are on same line).
                        $phpcsFile->fixer->addContent($parenthesisCloser, ' ');
                        $phpcsFile->fixer->endChangeset();
                    }
                } else {
                    $phpcsFile->addError($error, $parenthesisOpener, 'OpenBraceNotSameLine');
                }//end if
                return;
            }//end if
        }//end if

        if ($this->blank_line_check === true) {
            $firstContent = $phpcsFile->findNext(T_WHITESPACE, ($scopeOpener + 1), null, true);
            if ($tokens[$firstContent]['line'] > ($tokens[$scopeOpener]['line'] + 1)
                && in_array($tokens[$firstContent]['code'], array(T_CLOSE_TAG, T_COMMENT)) === false
            ) {
                $error = 'Blank line found at start of control structure';
                if (isset($phpcsFile->fixer) === true) {
                    $fix = $phpcsFile->addFixableError($error, $scopeOpener, 'BlankLineAfterStart');
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();

                        for ($i = ($scopeOpener + 1); $i < $firstContent; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->addNewline($scopeOpener);
                        $phpcsFile->fixer->endChangeset();
                    }
                } else {
                    $phpcsFile->addError($error, $scopeOpener, 'BlankLineAfterStart');
                }
            }

            $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($scopeCloser - 1), null, true);
            if ($tokens[$lastContent]['line'] !== ($tokens[$scopeCloser]['line'] - 1)) {
                $errorToken = $scopeCloser;
                for ($i = ($scopeCloser - 1); $i > $lastContent; $i--) {
                    if ($tokens[$i]['line'] < $tokens[$scopeCloser]['line']
                        && $tokens[$firstContent]['code'] !== T_OPEN_TAG
                    ) {
                        // TODO: Reporting error at empty line won't highlight it in IDE.
                        $error = 'Blank line found at end of control structure';
                        if (isset($phpcsFile->fixer) === true) {
                            $fix = $phpcsFile->addFixableError($error, $i, 'BlankLineBeforeEnd');
                            if ($fix === true) {
                                $phpcsFile->fixer->beginChangeset();

                                for ($j = ($lastContent + 1); $j < $scopeCloser; $j++) {
                                    $phpcsFile->fixer->replaceToken($j, '');
                                }

                                $phpcsFile->fixer->addNewlineBefore($scopeCloser);
                                $phpcsFile->fixer->endChangeset();
                            }
                        } else {
                            $phpcsFile->addError($error, $i, 'BlankLineBeforeEnd');
                        }//end if
                        break;
                    }//end if
                }//end for
            }//end if
        }//end if

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

            if ($this->blank_line_after_check === true
                && $tokens[$trailingContent]['line'] != ($tokens[$scopeCloser]['line'] + 1)
            ) {
                // TODO: Won't cover following case: "} echo 'OK';".
                $error = 'Blank line found after control structure';
                if (isset($phpcsFile->fixer) === true) {
                    $fix = $phpcsFile->addFixableError($error, $scopeCloser, 'BlankLineAfterEnd');
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();

                        for ($i = ($scopeCloser + 1); $i < $trailingContent; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        // TODO: Instead a separate error should be triggered when content comes right after closing brace.
                        $phpcsFile->fixer->addNewlineBefore($trailingContent);
                        $phpcsFile->fixer->endChangeset();
                    }
                } else {
                    $phpcsFile->addError($error, $scopeCloser, 'BlankLineAfterEnd');
                }
            }
        }//end if

    }//end process()


}//end class

?>
