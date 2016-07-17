<?php
/**
 * WordPress Coding Standard.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Restricts usage of some functions.
 *
 * @deprecated 0.1.0 The functionality which used to be contained in this class has been moved to
 *                   the WordPress_AbstractFunctionRestrictionsSniff class.
 *                   This class is left here to prevent backward-compatibility breaks for
 *                   custom sniffs extending the old class and references to this
 *                   sniff from custom phpcs.xml files.
 *
 * @category   PHP
 * @package    PHP_CodeSniffer
 * @author     Gary Jones
 */
class WordPress_Sniffs_HTML_TitleAttributeSniff implements PHP_CodeSniffer_Sniff {
	/**
	 * Registers the tokens that this sniff wants to listen for.
	 *
	 * An example return value for a sniff that wants to listen for whitespace
	 * and any comments would be:
	 *
	 * <code>
	 *    return array(
	 *            T_WHITESPACE,
	 *            T_DOC_COMMENT,
	 *            T_COMMENT,
	 *           );
	 * </code>
	 *
	 * @return int[]
	 * @see    Tokens.php
	 */
	public function register() {
		return array(
			T_INLINE_HTML
		);
	}

	/**
	 * Called when one of the token types that this sniff is listening for
	 * is found.
	 *
	 * The stackPtr variable indicates where in the stack the token was found.
	 * A sniff can acquire information this token, along with all the other
	 * tokens within the stack by first acquiring the token stack:
	 *
	 * <code>
	 *    $tokens = $phpcsFile->getTokens();
	 *    echo 'Encountered a '.$tokens[$stackPtr]['type'].' token';
	 *    echo 'token information: ';
	 *    print_r($tokens[$stackPtr]);
	 * </code>
	 *
	 * If the sniff discovers an anomaly in the code, they can raise an error
	 * by calling addError() on the PHP_CodeSniffer_File object, specifying an error
	 * message and the position of the offending token:
	 *
	 * <code>
	 *    $phpcsFile->addError('Encountered an error', $stackPtr);
	 * </code>
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where the
	 *                                        token was found.
	 * @param int                  $stackPtr  The position in the PHP_CodeSniffer
	 *                                        file's token stack where the token
	 *                                        was found.
	 *
	 * @return void|int Optionally returns a stack pointer. The sniff will not be
	 *                  called again on the current file until the returned stack
	 *                  pointer is reached. Return (count($tokens) + 1) to skip
	 *                  the rest of the file.
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		// This is far too simple and will catch too many non-errors.
		if ( false !== strpos( $tokens[$stackPtr]['content'], 'title=' ) ) {
			$phpcsFile->addError( 'Title attribute forbidden', $stackPtr, 'TitleAttribute' );
		}
	}
} // end class
