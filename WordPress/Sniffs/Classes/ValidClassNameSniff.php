<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Ensures classes are in camel caps, and the first letter is capitalised.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.1.0
 *
 * Last synced with base class ?[unknown date]? at commit ?[unknown commit]?.
 * @link    https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Squiz/Sniffs/Classes/ValidClassNameSniff.php
 */
class WordPress_Sniffs_Classes_ValidClassNameSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_CLASS,
			T_INTERFACE,
		);

	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The current file being processed.
	 * @param int                  $stackPtr  The position of the current token in the
	 *                                        stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		if ( ! isset( $tokens[ $stackPtr ]['scope_opener'] ) ) {
			$error	= 'Possible parse error: ';
			$error .= $tokens[ $stackPtr ]['content'];
			$error .= ' missing opening or closing brace';
			$phpcsFile->addWarning( $error, $stackPtr, 'MissingBrace' );
			return;
		}

		// Determine the name of the class or interface. Note that we cannot
		// simply look for the first T_STRING because a class name
		// starting with the number will be multiple tokens.
		$opener    = $tokens[ $stackPtr ]['scope_opener'];
		$nameStart = $phpcsFile->findNext( T_WHITESPACE, ( $stackPtr + 1 ), $opener, true );
		$nameEnd   = $phpcsFile->findNext( T_WHITESPACE, $nameStart, $opener );
		$name	   = trim( $phpcsFile->getTokensAsString( $nameStart, ( $nameEnd - $nameStart ) ) );

		// Check for camel caps format.
		$valid = PHP_CodeSniffer::isCamelCaps( str_replace( '_', '', $name ), true, true, false );
		if ( false === $valid ) {
			$type  = ucfirst( $tokens[ $stackPtr ]['content'] );
			$error = "$type name \"$name\" is not in camel caps format";
			$phpcsFile->addError( $error, $stackPtr, 'NotCamelCaps' );
		}

	} // end process()

} // End class.
