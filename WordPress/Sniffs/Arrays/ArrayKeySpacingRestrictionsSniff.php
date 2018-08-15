<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\Arrays;

use WordPress\Sniff;

/**
 * Check for proper spacing in array key references.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#space-usage
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.7.0  This sniff now has the ability to fix a number of the issues it flags.
 * @since   0.12.0 This class now extends WordPress_Sniff.
 * @since   0.13.0 Class name changed: this class is now namespaced.
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
			$this->phpcsFile->addWarning( 'Missing bracket closer.', $stackPtr, 'MissingBracketCloser' );
			return;
		}

		$need_spaces = $this->phpcsFile->findNext(
			array( \T_CONSTANT_ENCAPSED_STRING, \T_LNUMBER, \T_WHITESPACE, \T_MINUS ),
			( $stackPtr + 1 ),
			$token['bracket_closer'],
			true
		);

		$spaced1 = ( \T_WHITESPACE === $this->tokens[ ( $stackPtr + 1 ) ]['code'] );
		$spaced2 = ( \T_WHITESPACE === $this->tokens[ ( $token['bracket_closer'] - 1 ) ]['code'] );

		// It should have spaces unless if it only has strings or numbers as the key.
		if ( false !== $need_spaces && ! ( $spaced1 && $spaced2 ) ) {
			$error = 'Array keys must be surrounded by spaces unless they contain a string or an integer.';
			$fix   = $this->phpcsFile->addFixableError( $error, $stackPtr, 'NoSpacesAroundArrayKeys' );
			if ( true === $fix ) {
				if ( ! $spaced1 ) {
					$this->phpcsFile->fixer->addContentBefore( ( $stackPtr + 1 ), ' ' );
				}
				if ( ! $spaced2 ) {
					$this->phpcsFile->fixer->addContentBefore( $token['bracket_closer'], ' ' );
				}
			}
		} elseif ( false === $need_spaces && ( $spaced1 || $spaced2 ) ) {
			$error = 'Array keys must NOT be surrounded by spaces if they only contain a string or an integer.';
			$fix   = $this->phpcsFile->addFixableError( $error, $stackPtr, 'SpacesAroundArrayKeys' );
			if ( true === $fix ) {
				if ( $spaced1 ) {
					$this->phpcsFile->fixer->replaceToken( ( $stackPtr + 1 ), '' );
				}
				if ( $spaced2 ) {
					$this->phpcsFile->fixer->replaceToken( ( $token['bracket_closer'] - 1 ), '' );
				}
			}
		}
	}

}
