<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Ensure cast statements don't contain whitespace, but *are* surrounded by whitespace, based upon Squiz code.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#space-usage
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 *
 * Last synced with base class ?[unknown date]? at commit ?[unknown commit]?.
 * @link    https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Squiz/Sniffs/WhiteSpace/CastSpacingSniff.php
 */
class WordPress_Sniffs_WhiteSpace_CastStructureSpacingSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return PHP_CodeSniffer_Tokens::$castTokens;

	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token in the
	 *                                        stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		$content  = $tokens[ $stackPtr ]['content'];
		$expected = str_replace( ' ', '', $content );
		$expected = str_replace( "\t", '', $expected );

		if ( $content !== $expected ) {
			$error = 'Cast statements must not contain whitespace; expected "%s" but found "%s"';
			$data  = array(
				$expected,
				$content,
			);
			$phpcsFile->addWarning( $error, $stackPtr, 'ContainsWhiteSpace', $data );
		}

		if ( T_WHITESPACE !== $tokens[ ( $stackPtr - 1 ) ]['code'] ) {
			$error = 'No space before opening casting parenthesis is prohibited';
			$phpcsFile->addWarning( $error, $stackPtr, 'NoSpaceBeforeOpenParenthesis' );
		}

		if ( T_WHITESPACE !== $tokens[ ( $stackPtr + 1 ) ]['code'] ) {
			$error = 'No space after closing casting parenthesis is prohibited';
			$phpcsFile->addWarning( $error, $stackPtr, 'NoSpaceAfterCloseParenthesis' );
		}
	} // end process()

	/**
	 * If true, an error will be thrown; otherwise a warning.
	 *
	 * @var bool
	 */
	public $error = false;

} // End class.
