<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\PHP;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use WordPressCS\WordPress\Sniff;

/**
 * Enforces Yoda conditional statements.
 *
 * @link https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/#yoda-conditions
 *
 * @since 0.3.0
 * @since 0.12.0 This class now extends the WordPressCS native `Sniff` class.
 * @since 0.13.0 Class name changed: this class is now namespaced.
 */
final class YodaConditionsSniff extends Sniff {

	/**
	 * The tokens that indicate the start of a condition.
	 *
	 * @since 0.12.0
	 * @since 3.0.0  This property is now `private`.
	 *
	 * @var array
	 */
	private $condition_start_tokens;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {

		$starters                        = Tokens::$booleanOperators;
		$starters                       += Tokens::$assignmentTokens;
		$starters                       += Collections::ternaryOperators();
		$starters[ \T_CASE ]             = \T_CASE;
		$starters[ \T_RETURN ]           = \T_RETURN;
		$starters[ \T_SEMICOLON ]        = \T_SEMICOLON;
		$starters[ \T_OPEN_PARENTHESIS ] = \T_OPEN_PARENTHESIS;

		$this->condition_start_tokens = $starters;

		return array(
			\T_IS_EQUAL,
			\T_IS_NOT_EQUAL,
			\T_IS_IDENTICAL,
			\T_IS_NOT_IDENTICAL,
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

		$start = $this->phpcsFile->findPrevious( $this->condition_start_tokens, $stackPtr, null, false, null, true );

		$needs_yoda = false;

		// Note: going backwards!
		for ( $i = $stackPtr; $i > $start; $i-- ) {

			// Ignore whitespace.
			if ( isset( Tokens::$emptyTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			// If this is a variable or array assignment, we've seen all we need to see.
			if ( \T_VARIABLE === $this->tokens[ $i ]['code']
				|| \T_CLOSE_SQUARE_BRACKET === $this->tokens[ $i ]['code']
			) {
				$needs_yoda = true;
				break;
			}

			// If this is a function call or something, we are OK.
			if ( \T_CLOSE_PARENTHESIS === $this->tokens[ $i ]['code'] ) {
				return;
			}
		}

		if ( ! $needs_yoda ) {
			return;
		}

		// Check if this is a var to var comparison, e.g.: if ( $var1 == $var2 ).
		$next_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );

		if ( isset( Tokens::$castTokens[ $this->tokens[ $next_non_empty ]['code'] ] ) ) {
			$next_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $next_non_empty + 1 ), null, true );
		}

		if ( isset( Collections::ooHierarchyKeywords()[ $this->tokens[ $next_non_empty ]['code'] ] ) === true ) {
			$next_non_empty = $this->phpcsFile->findNext(
				( Tokens::$emptyTokens + array( \T_DOUBLE_COLON => \T_DOUBLE_COLON ) ),
				( $next_non_empty + 1 ),
				null,
				true
			);
		}

		if ( \T_VARIABLE === $this->tokens[ $next_non_empty ]['code'] ) {
			return;
		}

		$this->phpcsFile->addError( 'Use Yoda Condition checks, you must.', $stackPtr, 'NotYoda' );
	}
}
