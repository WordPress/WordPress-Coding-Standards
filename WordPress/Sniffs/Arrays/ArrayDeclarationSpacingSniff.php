<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

if ( ! class_exists( 'Squiz_Sniffs_Arrays_ArrayDeclarationSniff', true ) ) {
	throw new PHP_CodeSniffer_Exception( 'Class Squiz_Sniffs_Arrays_ArrayDeclarationSniff not found' );
}

/**
 * Enforces WordPress array format, based upon Squiz code.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#indentation
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0 The WordPress specific additional checks have now been split off
 *                 from the WordPress_Sniffs_Arrays_ArrayDeclaration sniff into
 *                 this sniff.
 *
 * {@internal This sniff only extends the upstream sniff to get the benefit of the
 * process logic which routes the processing to the single-line/multi-line methods.
 * Other than that, the actual sniffing from the upstream sniff is disregarded.
 * In other words: no real syncing with upstream necessary.}}
 *
 * Last synced with parent class October 5 2016 at commit ea32814346ecf29791de701b3fa464a9ca43f45b.
 * @link    https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Squiz/Sniffs/Arrays/ArrayDeclarationSniff.php
 */
class WordPress_Sniffs_Arrays_ArrayDeclarationSpacingSniff extends Squiz_Sniffs_Arrays_ArrayDeclarationSniff {

	/**
	 * Process a single line array.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Moved from WordPress_Sniffs_Arrays_ArrayDeclaration to this sniff.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile  The file being scanned.
	 * @param int                  $stackPtr   The position of the current token
	 *                                         in the stack passed in $tokens.
	 * @param int                  $arrayStart Position of the array opener in the token stack.
	 * @param int                  $arrayEnd   Position of the array closer in the token stack.
	 */
	public function processSingleLineArray( PHP_CodeSniffer_File $phpcsFile, $stackPtr, $arrayStart, $arrayEnd ) {

		// This array is empty, so the below checks aren't necessary.
		if ( ( $arrayStart + 1 ) === $arrayEnd ) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		// Check that there is a single space after the array opener.
		if ( T_WHITESPACE !== $tokens[ ( $arrayStart + 1 ) ]['code'] ) {

			$warning = 'Missing space after array opener.';
			$fix     = $phpcsFile->addFixableError( $warning, $arrayStart, 'NoSpaceAfterArrayOpener' );

			if ( true === $fix ) {
				$phpcsFile->fixer->addContent( $arrayStart, ' ' );
			}
		} elseif ( ' ' !== $tokens[ ( $arrayStart + 1 ) ]['content'] ) {

			$fix = $phpcsFile->addFixableError(
				'Expected 1 space after array opener, found %s.',
				$arrayStart,
				'SpaceAfterArrayOpener',
				array( strlen( $tokens[ ( $arrayStart + 1 ) ]['content'] ) )
			);

			if ( true === $fix ) {
				$phpcsFile->fixer->replaceToken( ( $arrayStart + 1 ), ' ' );
			}
		}

		if ( T_WHITESPACE !== $tokens[ ( $arrayEnd - 1 ) ]['code'] ) {

			$warning = 'Missing space before array closer.';
			$fix     = $phpcsFile->addFixableError( $warning, $arrayEnd, 'NoSpaceBeforeArrayCloser' );

			if ( true === $fix ) {
				$phpcsFile->fixer->addContentBefore( $arrayEnd, ' ' );
			}
		} elseif ( ' ' !== $tokens[ ( $arrayEnd - 1 ) ]['content'] ) {

			$fix = $phpcsFile->addFixableError(
				'Expected 1 space before array closer, found %s.',
				$arrayEnd,
				'SpaceBeforeArrayCloser',
				array( strlen( $tokens[ ( $arrayEnd - 1 ) ]['content'] ) )
			);

			if ( true === $fix ) {
				$phpcsFile->fixer->replaceToken( ( $arrayEnd - 1 ), ' ' );
			}
		}
	}

	/**
	 * (Don't) Process a multi-line array.
	 *
	 * {@internal Multi-line arrays are handled by the upstream sniff via the
	 * WordPress_Sniffs_Arrays_ArrayDeclaration sniff.}}
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile  The file being scanned.
	 * @param int                  $stackPtr   The position of the current token
	 *                                         in the stack passed in $tokens.
	 * @param int                  $arrayStart Position of the array opener in the token stack.
	 * @param int                  $arrayEnd   Position of the array closer in the token stack.
	 */
	public function processMultiLineArray( PHP_CodeSniffer_File $phpcsFile, $stackPtr, $arrayStart, $arrayEnd ) {
		return;
	} // End processMultiLineArray().

} // End class.
