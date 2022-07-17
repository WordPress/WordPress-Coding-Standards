<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\Arrays;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Fixers\SpacesFixer;
use WordPressCS\WordPress\Sniff;

/**
 * Check for proper spacing in array key references.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#space-usage
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.7.0  This sniff now has the ability to fix a number of the issues it flags.
 * @since   0.12.0 This class now extends the WordPressCS native `Sniff` class.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   2.2.0  The sniff now also checks the size of the spacing, if applicable.
 */
class ArrayKeySpacingRestrictionsSniff extends Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			\T_OPEN_SQUARE_BRACKET,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {

		$token = $this->tokens[ $stackPtr ];
		if ( ! isset( $token['bracket_closer'] ) ) {
			return;
		}

		/*
		 * Handle square brackets without a key (array assignments) first.
		 */
		$first_non_ws = $this->phpcsFile->findNext( \T_WHITESPACE, ( $stackPtr + 1 ), null, true );
		if ( $first_non_ws === $token['bracket_closer'] ) {
			$error = 'There should be %1$s between the square brackets for an array assignment without an explicit key. Found: %2$s';
			SpacesFixer::checkAndFix(
				$this->phpcsFile,
				$stackPtr,
				$token['bracket_closer'],
				0,
				$error,
				'SpacesBetweenBrackets'
			);

			return;
		}

		/*
		 * Handle the spaces around explicit array keys.
		 */
		$needs_spaces = true;

		// Skip over a potential plus/minus sign for integers.
		$first_effective = $first_non_ws;
		if ( \T_MINUS === $this->tokens[ $first_effective ]['code'] || \T_PLUS === $this->tokens[ $first_effective ]['code'] ) {
			$first_effective = $this->phpcsFile->findNext( \T_WHITESPACE, ( $first_effective + 1 ), null, true );
		}

		$next_non_ws = $this->phpcsFile->findNext( \T_WHITESPACE, ( $first_effective + 1 ), null, true );
		if ( ( \T_CONSTANT_ENCAPSED_STRING === $this->tokens[ $first_effective ]['code']
			|| \T_LNUMBER === $this->tokens[ $first_effective ]['code'] )
			&& $next_non_ws === $token['bracket_closer']
		) {
			$needs_spaces = false;
		}

		$has_space_after_opener = ( \T_WHITESPACE === $this->tokens[ ( $stackPtr + 1 ) ]['code'] );
		$has_space_before_close = ( \T_WHITESPACE === $this->tokens[ ( $token['bracket_closer'] - 1 ) ]['code'] );

		// It should have spaces unless if it only has strings or numbers as the key.
		if ( true === $needs_spaces
			&& ( false === $has_space_after_opener || false === $has_space_before_close )
		) {
			$error = 'Array keys must be surrounded by spaces unless they contain a string or an integer.';
			$fix   = $this->phpcsFile->addFixableError( $error, $stackPtr, 'NoSpacesAroundArrayKeys' );
			if ( true === $fix ) {
				if ( ! $has_space_after_opener ) {
					$this->phpcsFile->fixer->addContentBefore( ( $stackPtr + 1 ), ' ' );
				}
				if ( ! $has_space_before_close ) {
					$this->phpcsFile->fixer->addContentBefore( $token['bracket_closer'], ' ' );
				}
			}
		} elseif ( false === $needs_spaces && ( $has_space_after_opener || $has_space_before_close ) ) {
			$error = 'Array keys must NOT be surrounded by spaces if they only contain a string or an integer.';
			$fix   = $this->phpcsFile->addFixableError( $error, $stackPtr, 'SpacesAroundArrayKeys' );
			if ( true === $fix ) {
				if ( $has_space_after_opener ) {
					$this->phpcsFile->fixer->beginChangeset();
					$this->phpcsFile->fixer->replaceToken( ( $stackPtr + 1 ), '' );

					for ( $i = ( $stackPtr + 2 ); $i < $token['bracket_closer']; $i++ ) {
						if ( \T_WHITESPACE !== $this->tokens[ $i ]['code'] ) {
							break;
						}

						$this->phpcsFile->fixer->replaceToken( $i, '' );
					}

					$this->phpcsFile->fixer->endChangeset();
				}
				if ( $has_space_before_close ) {
					$this->phpcsFile->fixer->beginChangeset();
					$this->phpcsFile->fixer->replaceToken( ( $token['bracket_closer'] - 1 ), '' );

					for ( $i = ( $token['bracket_closer'] - 2 ); $i > $stackPtr; $i-- ) {
						if ( \T_WHITESPACE !== $this->tokens[ $i ]['code'] ) {
							break;
						}

						$this->phpcsFile->fixer->replaceToken( $i, '' );
					}

					$this->phpcsFile->fixer->endChangeset();
				}
			}
		}

		// If spaces are needed, check that there is only one space.
		if ( true === $needs_spaces && ( $has_space_after_opener || $has_space_before_close ) ) {
			if ( $has_space_after_opener ) {
				$ptr    = ( $stackPtr + 1 );
				$length = 0;
				if ( $this->tokens[ $ptr ]['line'] !== $this->tokens[ ( $ptr + 1 ) ]['line'] ) {
					$length = 'newline';
				} else {
					$length = $this->tokens[ $ptr ]['length'];
				}

				if ( 1 !== $length ) {
					$error = 'There should be exactly one space before the array key. Found: %s';
					$data  = array( $length );
					$fix   = $this->phpcsFile->addFixableError(
						$error,
						$ptr,
						'TooMuchSpaceBeforeKey',
						$data
					);

					if ( true === $fix ) {
						$this->phpcsFile->fixer->beginChangeset();
						$this->phpcsFile->fixer->replaceToken( $ptr, ' ' );

						for ( $i = ( $ptr + 1 ); $i < $token['bracket_closer']; $i++ ) {
							if ( \T_WHITESPACE !== $this->tokens[ $i ]['code'] ) {
								break;
							}

							$this->phpcsFile->fixer->replaceToken( $i, '' );
						}

						$this->phpcsFile->fixer->endChangeset();
					}
				}
			}

			if ( $has_space_before_close ) {
				$prev_non_empty = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $token['bracket_closer'] - 1 ), null, true );
				$ptr            = ( $prev_non_empty + 1 );
				$length         = 0;
				if ( $this->tokens[ $ptr ]['line'] !== $this->tokens[ $token['bracket_closer'] ]['line'] ) {
					$length = 'newline';
				} else {
					$length = $this->tokens[ $ptr ]['length'];
				}

				if ( 1 !== $length ) {
					$error = 'There should be exactly one space after the array key. Found: %s';
					$data  = array( $length );
					$fix   = $this->phpcsFile->addFixableError(
						$error,
						$ptr,
						'TooMuchSpaceAfterKey',
						$data
					);

					if ( true === $fix ) {
						$this->phpcsFile->fixer->beginChangeset();
						$this->phpcsFile->fixer->replaceToken( $ptr, ' ' );

						for ( $i = ( $ptr + 1 ); $i < $token['bracket_closer']; $i++ ) {
							if ( \T_WHITESPACE !== $this->tokens[ $i ]['code'] ) {
								break;
							}

							$this->phpcsFile->fixer->replaceToken( $i, '' );
						}

						$this->phpcsFile->fixer->endChangeset();
					}
				}
			}
		}
	}

}
