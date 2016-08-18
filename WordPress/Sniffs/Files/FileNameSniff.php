<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Ensures filenames do not contain underscores.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.1.0
 */
class WordPress_Sniffs_Files_FileNameSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array( T_OPEN_TAG );

	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token in the
	 *                                        stack passed in $tokens.
	 *
	 * @return int
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

		$fileName = basename( $phpcsFile->getFileName() );

		if ( false !== strpos( $fileName, '_' ) ) {
			$expected = str_replace( '_', '-', $fileName );
			$error    = 'Filename "' . $fileName . '" with underscores found; use ' . $expected . ' instead';
			$phpcsFile->addError( $error, $stackPtr, 'UnderscoresNotAllowed' );
		}

		return ( $phpcsFile->numTokens + 1 );

	} // end process()

} // End class.
