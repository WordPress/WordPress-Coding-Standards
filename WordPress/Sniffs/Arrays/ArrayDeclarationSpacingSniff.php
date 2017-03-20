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
 * @since   0.11.0 - The WordPress specific additional checks have now been split off
 *                 from the WordPress_Sniffs_Arrays_ArrayDeclaration sniff into
 *                 this sniff.
 *                 - Added sniffing & fixing for associative arrays.
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

		$array_has_keys = $phpcsFile->findNext( T_DOUBLE_ARROW, $arrayStart, $arrayEnd );
		if ( false !== $array_has_keys ) {
			$fix = $phpcsFile->addFixableError(
				'When an array uses associative keys, each value should start on a new line.',
				$arrayEnd,
				'AssociativeKeyFound'
			);

			if ( true === $fix ) {
				// Only deal with one nesting level per loop to have the best chance of getting the indentation right.
				static $current_loop = array();

				if ( ! isset( $current_loop[ $phpcsFile->fixer->loops ] ) ) {
					$current_loop[ $phpcsFile->fixer->loops ] = array_fill( 0, $phpcsFile->numTokens, false );
				}

				if ( false === $current_loop[ $phpcsFile->fixer->loops ][ $arrayStart ] ) {
					for ( $i = $arrayStart; $i <= $arrayEnd; $i++ ) {
						$current_loop[ $phpcsFile->fixer->loops ][ $i ] = true;
					}

					$this->fix_associative_array( $phpcsFile, $arrayStart, $arrayEnd );
				}
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

	/**
	 * Create & apply a changeset for a single line array with associative keys.
	 *
	 * @since 0.11.0
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile  The file being scanned.
	 * @param int                  $arrayStart Position of the array opener in the token stack.
	 * @param int                  $arrayEnd   Position of the array closer in the token stack.
	 * @return void
	 */
	protected function fix_associative_array( PHP_CodeSniffer_File $phpcsFile, $arrayStart, $arrayEnd ) {

		$tokens = $phpcsFile->getTokens();

		// Determine the needed indentation.
		$indentation = '';
		for ( $i = $arrayStart; $i >= 0; $i-- ) {
			if ( $tokens[ $i ]['line'] === $tokens[ $arrayStart ]['line'] ) {
				continue;
			}

			if ( T_WHITESPACE === $tokens[ ( $i + 1 ) ]['code'] ) {
				// Something weird going on with tabs vs spaces, but this fixes it.
				$indentation = str_replace( '    ', "\t", $tokens[ ( $i + 1 ) ]['content'] );
			}
			break;
		}
		unset( $i );

		$value_indentation = "\t" . $indentation;

		// Which nesting level is the one we are interested in ?
		$nesting_count = 1;
		if ( T_OPEN_SHORT_ARRAY === $tokens[ $arrayStart ]['code'] ) {
			$nesting_count = 0;
		}

		if ( isset( $tokens[ $arrayStart ]['nested_parenthesis'] ) ) {
			$nesting_count += count( $tokens[ $arrayStart ]['nested_parenthesis'] );
		}

		// Record the required changes.
		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->addNewline( $arrayStart );
		if ( T_WHITESPACE === $tokens[ ( $arrayStart + 1 ) ]['code'] ) {
			$phpcsFile->fixer->replaceToken( ( $arrayStart + 1 ), $value_indentation );
		} else {
			$phpcsFile->fixer->addContentBefore( ( $arrayStart + 1 ), $value_indentation );
		}

		for ( $ptr = ( $arrayStart + 1 ); $ptr < $arrayEnd; $ptr++ ) {
			$ptr = $phpcsFile->findNext( array( T_COMMA, T_OPEN_SHORT_ARRAY ), $ptr, $arrayEnd );

			if ( false === $ptr ) {
				break;
			}

			// Ignore anything within short array definition brackets.
			// Necessary as the nesting level in that case is still the same.
			if ( 'T_OPEN_SHORT_ARRAY' === $tokens[ $ptr ]['type']
				&& ( isset( $tokens[ $ptr ]['bracket_opener'] )
					&& $tokens[ $ptr ]['bracket_opener'] === $ptr )
				&& isset( $tokens[ $ptr ]['bracket_closer'] )
			) {
				$ptr = $tokens[ $ptr ]['bracket_closer'];
				continue;
			}

			// Ignore comma's at a lower nesting level.
			if ( 'T_COMMA' === $tokens[ $ptr ]['type']
				&& isset( $tokens[ $ptr ]['nested_parenthesis'] )
				&& count( $tokens[ $ptr ]['nested_parenthesis'] ) !== $nesting_count
			) {
				continue;
			}

			$phpcsFile->fixer->addNewline( $ptr );
			if ( isset( $tokens[ ( $ptr + 1 ) ] ) ) {
				if ( T_WHITESPACE === $tokens[ ( $ptr + 1 ) ]['code'] ) {
					$phpcsFile->fixer->replaceToken( ( $ptr + 1 ), $value_indentation );
				} else {
					$phpcsFile->fixer->addContentBefore( ( $ptr + 1 ), $value_indentation );
				}
			}
		}

		$token_before_end = $phpcsFile->findPrevious( PHP_CodeSniffer_Tokens::$emptyTokens, ( $arrayEnd - 1 ), $arrayStart, true, null, true );
		if ( 'T_COMMA' !== $tokens[ $token_before_end ]['type'] ) {
			$phpcsFile->fixer->addContent( $token_before_end, ',' );

			if ( T_WHITESPACE === $tokens[ ( $arrayEnd - 1 ) ]['code'] || "\n" === $phpcsFile->fixer->getTokenContent( ( $arrayEnd - 1 ) ) ) {
				$phpcsFile->fixer->replaceToken( ( $arrayEnd - 1 ), "\n" . $indentation );
			} else {
				$phpcsFile->fixer->addContentBefore( $arrayEnd, "\n" . $indentation );
			}
		} elseif ( $value_indentation === $phpcsFile->fixer->getTokenContent( ( $arrayEnd - 1 ) ) ) {
			$phpcsFile->fixer->replaceToken( ( $arrayEnd - 1 ), $indentation );
		}

		$phpcsFile->fixer->endChangeset();
	} // End fix_associative_array().

} // End class.
