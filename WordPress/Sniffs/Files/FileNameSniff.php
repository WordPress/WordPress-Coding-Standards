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
 * @since   0.11.0 - This sniff will now also check for all lowercase file names.
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

		$file     = $phpcsFile->getFileName();
		$fileName = basename( $file );
		$expected = strtolower( str_replace( '_', '-', $fileName ) );

		/*
		 * Generic check for lowercase hyphenated file names.
		 */
		if ( $fileName !== $expected ) {
			$phpcsFile->addError(
				'Filenames should be all lowercase with hyphens as word separators. Expected %s, but found %s.',
				0,
				'NotHyphenatedLowercase',
				array( $expected, $fileName )
			);
		}
		unset( $expected );

		// Only run this sniff once per file, no need to run it again.
		return ( $phpcsFile->numTokens + 1 );

	} // End process().

} // End class.
