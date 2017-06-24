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
	 * Token this sniff targets.
	 *
	 * Also used for distinguishing between the array and an array value
	 * which is also an array.
	 *
	 * @since 0.12.0
	 *
	 * @var array
	 */
	private $targets = array(
		T_ARRAY            => T_ARRAY,
		T_OPEN_SHORT_ARRAY => T_OPEN_SHORT_ARRAY,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 0.12.0
	 *
	 * @return array
	 */
	public function register() {
		return $this->targets;
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
		unset( $array_open_close );

		// This array is empty, so the below checks aren't necessary.
		if ( ( $opener + 1 ) === $closer ) {
			return;
		}

		// We're only interested in single-line arrays.
		if ( $this->tokens[ $opener ]['line'] !== $this->tokens[ $closer ]['line'] ) {
			return;
		}

		/*
		 * Check that associative arrays are always multi-line.
		 */
		$array_has_keys = $this->phpcsFile->findNext( T_DOUBLE_ARROW, $opener, $closer );
		if ( false !== $array_has_keys ) {

			$array_items = $this->get_function_call_parameters( $stackPtr );

			if ( ! empty( $array_items ) ) {
				/*
				 * Make sure the double arrow is for *this* array, not for a nested one.
				 */
				$array_has_keys = false; // Reset before doing more detailed check.
				foreach ( $array_items as $item ) {
					for ( $ptr = $item['start']; $ptr <= $item['end']; $ptr++ ) {
						if ( T_DOUBLE_ARROW === $this->tokens[ $ptr ]['code'] ) {
							$array_has_keys = true;
							break 2;
						}

						// Skip passed any nested arrays.
						if ( isset( $this->targets[ $this->tokens[ $ptr ]['code'] ] ) ) {
							$nested_array_open_close = $this->find_array_open_close( $ptr );
							if ( false === $nested_array_open_close ) {
								// Nested array open/close could not be determined.
								continue;
							}

							$ptr = $nested_array_open_close['closer'];
						}
					}
				}

				if ( true === $array_has_keys ) {

					$fix = $this->phpcsFile->addFixableError(
						'When an array uses associative keys, each value should start on a new line.',
						$closer,
						'AssociativeKeyFound'
					);

					if ( true === $fix ) {

						$this->phpcsFile->fixer->beginChangeset();

						foreach ( $array_items as $item ) {
							/*
							 * Add a line break before the first non-empty token in the array item.
							 * Prevents extraneous whitespace at the start of the line which could be
							 * interpreted as alignment whitespace.
							 */
							$first_non_empty = $this->phpcsFile->findNext(
								PHP_CodeSniffer_Tokens::$emptyTokens,
								$item['start'],
								( $item['end'] + 1 ),
								true
							);
							if ( false === $first_non_empty ) {
								continue;
							}

							if ( $item['start'] <= ( $first_non_empty - 1 )
								&& T_WHITESPACE === $this->tokens[ ( $first_non_empty - 1 ) ]['code']
							) {
								// Remove whitespace which would otherwise becoming trailing
								// (as it gives problems with the fixed file).
								$this->phpcsFile->fixer->replaceToken( ( $first_non_empty - 1 ), '' );
							}

							$this->phpcsFile->fixer->addNewlineBefore( $first_non_empty );
						}

						$this->phpcsFile->fixer->endChangeset();
					}

					// No need to check for spacing around parentheses as this array should be multi-line.
					return;
				}
			}
		}

		/*
		 * Check that there is a single space after the array opener and before the array closer.
		 */
		if ( T_WHITESPACE !== $this->tokens[ ( $opener + 1 ) ]['code'] ) {

			$fix = $this->phpcsFile->addFixableError(
				'Missing space after array opener.',
				$opener,
				'NoSpaceAfterArrayOpener'
			);

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

			$fix = $this->phpcsFile->addFixableError(
				'Missing space before array closer.',
				$closer,
				'NoSpaceBeforeArrayCloser'
			);

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
	}

} // End class.
