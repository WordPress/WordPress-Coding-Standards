<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\PHP;

use WordPress\Sniff;

/**
 * Checks for whitespace before first open tag in a PHP file.
 *
 * Loosely based on the PHPCS native `Generic.PHP.CharacterBeforePHPOpeningTag` sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   1.0.0
 */
class WhitespaceBeforePHPOpenTagSniff extends Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_OPEN_TAG,
			T_OPEN_TAG_WITH_ECHO,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int Stack pointer to the end of the file.
	 *             We're only interested in the first PHP open tag.
	 */
	public function process_token( $stackPtr ) {
		$expected = 0;
		if ( $stackPtr > 0 ) {
			// Allow for a shebang line.
			if ( substr( $this->tokens[0]['content'], 0, 2 ) === '#!' ) {
				$expected = 1;
			}
		}

		if ( $stackPtr === $expected ) {
			return $this->phpcsFile->numTokens;
		}

		// T_INLINE_HTML are the only tokens which we would expect before the open tag.
		$unexpected_token = $this->phpcsFile->findPrevious( T_INLINE_HTML, ( $stackPtr - 1 ), null, true );
		if ( false !== $unexpected_token ) {
			$this->phpcsFile->addError( 'Unexpected code found before the PHP open tag.', $unexpected_token, 'CodeFound' );
			return $this->phpcsFile->numTokens;
		}

		// Check for non-whitespace characters.
		for ( $i = ( $stackPtr - 1 ); $i >= 0; $i-- ) {
			if ( preg_match( '`^[\pZ\s]+$`u', $this->tokens[ $i ]['content'] ) !== 1 ) {
				// Non-whitespace found.
				return $this->phpcsFile->numTokens;
			}
		}

		$fix = $this->phpcsFile->addFixableError( 'Whitespace found before the PHP open tag.', 0, 'Found' );
		if ( true === $fix ) {
			$this->phpcsFile->fixer->beginChangeset();
			for ( $i = 0; $i < $stackPtr; $i++ ) {
				$this->phpcsFile->fixer->replaceToken( $i, '' );
			}
			$this->phpcsFile->fixer->endChangeset();
		}

		// Skip the rest of the file.
		return $this->phpcsFile->numTokens;
	}

}
