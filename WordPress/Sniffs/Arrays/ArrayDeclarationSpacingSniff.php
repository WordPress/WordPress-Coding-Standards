<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Enforces WordPress array spacing format.
 *
 * WordPress specific checks which are not covered by the `WordPress.Arrays.ArrayDeclaration`/
 * `Squiz.Arrays.ArrayDeclaration` sniff.
 *
 * - Checks for one space after the array opener / before the array closer in single-line arrays.
 * - Checks that associative arrays are multi-line.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#indentation
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0 - The WordPress specific additional checks have now been split off
 *                 from the WordPress_Sniffs_Arrays_ArrayDeclaration sniff into
 *                 this sniff.
 *                 - Added sniffing & fixing for associative arrays.
 * @since   0.12.0 Decoupled this sniff from the upstream sniff completely.
 *                 This sniff now extends the `WordPress_Sniff` instead.
 */
class WordPress_Sniffs_Arrays_ArrayDeclarationSpacingSniff extends WordPress_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 0.12.0
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_ARRAY,
			T_OPEN_SHORT_ARRAY,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 0.12.0 The actual checks contained in this method used to
	 *               be in the `processSingleLineArray()` method.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {

		/*
		 * Determine the array opener & closer.
		 */
		$array_open_close = $this->find_array_open_close( $stackPtr );
		if ( false === $array_open_close ) {
			// Array open/close could not be determined.
			return;
		}

		$opener = $array_open_close['opener'];
		$closer = $array_open_close['closer'];

		// This array is empty, so the below checks aren't necessary.
		if ( ( $opener + 1 ) === $closer ) {
			return;
		}

		// We're only interested in single-line arrays.
		if ( $this->tokens[ $opener ]['line'] !== $this->tokens[ $closer ]['line'] ) {
			return;
		}

		// Check that there is a single space after the array opener.
		if ( T_WHITESPACE !== $this->tokens[ ( $opener + 1 ) ]['code'] ) {

			$warning = 'Missing space after array opener.';
			$fix     = $this->phpcsFile->addFixableError( $warning, $opener, 'NoSpaceAfterArrayOpener' );

			if ( true === $fix ) {
				$this->phpcsFile->fixer->addContent( $opener, ' ' );
			}
		} elseif ( ' ' !== $this->tokens[ ( $opener + 1 ) ]['content'] ) {

			$fix = $this->phpcsFile->addFixableError(
				'Expected 1 space after array opener, found %s.',
				$opener,
				'SpaceAfterArrayOpener',
				array( strlen( $this->tokens[ ( $opener + 1 ) ]['content'] ) )
			);

			if ( true === $fix ) {
				$this->phpcsFile->fixer->replaceToken( ( $opener + 1 ), ' ' );
			}
		}

		if ( T_WHITESPACE !== $this->tokens[ ( $closer - 1 ) ]['code'] ) {

			$warning = 'Missing space before array closer.';
			$fix     = $this->phpcsFile->addFixableError( $warning, $closer, 'NoSpaceBeforeArrayCloser' );

			if ( true === $fix ) {
				$this->phpcsFile->fixer->addContentBefore( $closer, ' ' );
			}
		} elseif ( ' ' !== $this->tokens[ ( $closer - 1 ) ]['content'] ) {

			$fix = $this->phpcsFile->addFixableError(
				'Expected 1 space before array closer, found %s.',
				$closer,
				'SpaceBeforeArrayCloser',
				array( strlen( $this->tokens[ ( $closer - 1 ) ]['content'] ) )
			);

			if ( true === $fix ) {
				$this->phpcsFile->fixer->replaceToken( ( $closer - 1 ), ' ' );
			}
		}

		$array_has_keys = $this->phpcsFile->findNext( T_DOUBLE_ARROW, $opener, $closer );
		if ( false !== $array_has_keys ) {
			$fix = $this->phpcsFile->addFixableError(
				'When an array uses associative keys, each value should start on a new line.',
				$closer,
				'AssociativeKeyFound'
			);

			if ( true === $fix ) {
				// Only deal with one nesting level per loop to have the best chance of getting the indentation right.
				static $current_loop = array();

				if ( ! isset( $current_loop[ $this->phpcsFile->fixer->loops ] ) ) {
					$current_loop[ $this->phpcsFile->fixer->loops ] = array_fill( 0, $this->phpcsFile->numTokens, false );
				}

				if ( false === $current_loop[ $this->phpcsFile->fixer->loops ][ $opener ] ) {
					for ( $i = $opener; $i <= $closer; $i++ ) {
						$current_loop[ $this->phpcsFile->fixer->loops ][ $i ] = true;
					}

					$this->fix_associative_array( $this->phpcsFile, $opener, $closer );
				}
			}
		}
	}

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
				// If the tokenizer replaced tabs with spaces, use the original content.
				$indentation = $tokens[ ( $i + 1 ) ]['content'];
				if ( isset( $tokens[ ( $i + 1 ) ]['orig_content'] ) ) {
					$indentation = $tokens[ ( $i + 1 ) ]['orig_content'];
				}
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
