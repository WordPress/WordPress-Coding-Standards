<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Checks that the opening brace of a class or interface is on the same line
 * as the class declaration.
 *
 * Also checks that the brace is the last thing on that line and has precisely one space before it.
 *
 * @link      https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#brace-style
 *
 * @package   WPCS\WordPressCodingStandards
 *
 * @since     0.10.0
 *
 * {@internal Upstream PR https://github.com/squizlabs/PHP_CodeSniffer/pull/1070 has been merged.
 *            If and when the WPCS minimum PHPCS version would be upped to the version
 *            that PR is contained in - probably v 2.7.0 -, this sniff and associated unit tests
 *            can be replaced by the upstream sniff Generic.Classes.OpeningBraceSameLine.}}
 */
class WordPress_Sniffs_Classes_ClassOpeningStatementSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_CLASS,
			T_INTERFACE,
			T_TRAIT,
		);

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
		$tokens           = $phpcsFile->getTokens();
		$scope_identifier = $phpcsFile->findNext( T_STRING, ( $stackPtr + 1 ) );
		$errorData        = array(
			strtolower( $tokens[ $stackPtr ]['content'] ) . ' ' . $tokens[ $scope_identifier ]['content']
		);

		if ( ! isset( $tokens[ $stackPtr ]['scope_opener'] ) ) {
			$error = 'Possible parse error: %s missing opening or closing brace';
			$phpcsFile->addWarning( $error, $stackPtr, 'MissingBrace', $errorData );
			return;
		}

		$openingBrace = $tokens[ $stackPtr ]['scope_opener'];

		/*
		 * Is the brace on the same line as the class/interface/trait declaration ?
		 */
		$lastClassLineToken = $phpcsFile->findPrevious( T_STRING, ( $openingBrace - 1 ), $stackPtr );
		$lastClassLine      = $tokens[ $lastClassLineToken ]['line'];
		$braceLine          = $tokens[ $openingBrace ]['line'];
		$lineDifference     = ( $braceLine - $lastClassLine );

		if ( $lineDifference > 0 ) {
			$phpcsFile->recordMetric( $stackPtr, 'Class opening brace placement', 'new line' );
			$error = 'Opening brace should be on the same line as the declaration for %s';
			$fix   = $phpcsFile->addFixableError( $error, $openingBrace, 'BraceOnNewLine', $errorData );
			if ( true === $fix ) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent( $lastClassLineToken, ' {' );
				$phpcsFile->fixer->replaceToken( $openingBrace, '' );
				$phpcsFile->fixer->endChangeset();
			}
		} else {
			$phpcsFile->recordMetric( $stackPtr, 'Class opening brace placement', 'same line' );
		}

		/*
		 * Is the opening brace the last thing on the line ?
		 */
		$next = $phpcsFile->findNext( T_WHITESPACE, ( $openingBrace + 1 ), null, true );
		if ( $tokens[ $next ]['line'] === $tokens[ $openingBrace ]['line'] ) {
			if ( $next === $tokens[ $stackPtr ]['scope_closer'] ) {
				// Ignore empty classes.
				return;
			}

			$error = 'Opening brace must be the last content on the line';
			$fix   = $phpcsFile->addFixableError( $error, $openingBrace, 'ContentAfterBrace' );
			if ( true === $fix ) {
				$phpcsFile->fixer->addNewline( $openingBrace );
			}
		}

		// Only continue checking if the opening brace looks good.
		if ( $lineDifference > 0 ) {
			return;
		}

		/*
		 * Is there precisely one space before the opening brace ?
		 */
		if ( T_WHITESPACE !== $tokens[ ( $openingBrace - 1 ) ]['code'] ) {
			$length = 0;
		} elseif ( "\t" === $tokens[ ( $openingBrace - 1 ) ]['content'] ) {
			$length = '\t';
		} else {
			$length = strlen( $tokens[ ( $openingBrace - 1 ) ]['content'] );
		}

		if ( 1 !== $length ) {
			$error = 'Expected 1 space before opening brace; found %s';
			$data  = array( $length );
			$fix   = $phpcsFile->addFixableError( $error, $openingBrace, 'SpaceBeforeBrace', $data );
			if ( true === $fix ) {
				if ( 0 === $length || '\t' === $length ) {
					$phpcsFile->fixer->addContentBefore( $openingBrace, ' ' );
				} else {
					$phpcsFile->fixer->replaceToken( ( $openingBrace - 1 ), ' ' );
				}
			}
		}

	} // End process().

} // End class.
