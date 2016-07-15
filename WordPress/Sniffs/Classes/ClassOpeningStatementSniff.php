<?php
/**
 * WordPress Coding Standard.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * WordPress_Classes_ClassOpeningStatementSniff.
 *
 * Checks that the opening brace of a class or interface is on the same line
 * as the class declaration.
 *
 * Also checks that the brace is the last thing on that line and has precisely one space before it.
 *
 * Loosely based on Generic_Sniffs_Functions_OpeningFunctionBraceKernighanRitchieSniff.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
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

	} // end register()

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
		$classLine      = $tokens[ $stackPtr ]['line'];
		$braceLine      = $tokens[ $openingBrace ]['line'];
		$lineDifference = ( $braceLine - $classLine );

		if ( $lineDifference > 0 ) {
			$phpcsFile->recordMetric( $stackPtr, 'Class opening brace placement', 'new line' );
			$error = 'Opening brace should be on the same line as the declaration for %s';
			$fix   = $phpcsFile->addFixableError( $error, $openingBrace, 'BraceOnNewLine', $errorData );
			if ( true === $fix ) {
				$prev = $phpcsFile->findPrevious( T_STRING, ( $openingBrace - 1 ), $stackPtr );

				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent( $prev, ' {' );
				$phpcsFile->fixer->replaceToken( $openingBrace, '' );
				$phpcsFile->fixer->endChangeset();

				unset( $prev );
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

	} // end process()

} // end class
