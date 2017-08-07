<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\CodeAnalysis;

use WordPress\Sniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detects variable assignments being made within conditions.
 *
 * This is a typical code smell and more often than not a comparison was intended.
 *
 * Note: this sniff does not detect variable assignments in the conditional part of ternaries!
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 *
 * {@internal This sniff is a duplicate of the same sniff as pulled upstream.
 * Once the upstream sniff has been merged and the minimum WPCS PHPCS requirement has gone up to
 * the version in which the sniff was merged, this version can be safely removed.
 * {@link https://github.com/squizlabs/PHP_CodeSniffer/pull/1594} }}
 */
class AssignmentInConditionSniff extends Sniff {

	/**
	 * Assignment tokens to trigger on.
	 *
	 * Set in the register() method.
	 *
	 * @since 0.14.0
	 *
	 * @var array
	 */
	protected $assignment_tokens = array();

	/**
	 * The tokens that indicate the start of a condition.
	 *
	 * @since 0.14.0
	 *
	 * @var array
	 */
	protected $condition_start_tokens = array();

	/**
	 * Registers the tokens that this sniff wants to listen for.
	 *
	 * @since 0.14.0
	 *
	 * @return array
	 */
	public function register() {
		$this->assignment_tokens = Tokens::$assignmentTokens;
		unset( $this->assignment_tokens[ T_DOUBLE_ARROW ] );

		$starters                       = Tokens::$booleanOperators;
		$starters[ T_SEMICOLON ]        = T_SEMICOLON;
		$starters[ T_OPEN_PARENTHESIS ] = T_OPEN_PARENTHESIS;

		$this->condition_start_tokens = $starters;

		return array(
			T_IF,
			T_ELSEIF,
			T_FOR,
			T_SWITCH,
			T_CASE,
			T_WHILE,
		);

	}//end register()

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 0.14.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {

		$token = $this->tokens[ $stackPtr ];

		// Find the condition opener/closer.
		if ( T_FOR === $token['code'] ) {
			if ( isset( $token['parenthesis_opener'], $token['parenthesis_closer'] ) === false ) {
				return;
			}

			$semicolon = $this->phpcsFile->findNext( T_SEMICOLON, ( $token['parenthesis_opener'] + 1 ), $token['parenthesis_closer'] );
			if ( false === $semicolon ) {
				return;
			}

			$opener    = $semicolon;
			$semicolon = $this->phpcsFile->findNext( T_SEMICOLON, ( $opener + 1 ), $token['parenthesis_closer'] );
			if ( false === $semicolon ) {
				return;
			}

			$closer = $semicolon;
			unset( $semicolon );

		} elseif ( T_CASE === $token['code'] ) {
			if ( isset( $token['scope_opener'] ) === false ) {
				return;
			}

			$opener = $stackPtr;
			$closer = $token['scope_opener'];

		} else {
			if ( isset( $token['parenthesis_opener'], $token['parenthesis_closer'] ) === false ) {
				return;
			}

			$opener = $token['parenthesis_opener'];
			$closer = $token['parenthesis_closer'];
		}

		$startPos = $opener;

		do {
			$hasAssignment = $this->phpcsFile->findNext( $this->assignment_tokens, ( $startPos + 1 ), $closer );
			if ( false === $hasAssignment ) {
				return;
			}

			// Examine whether the left side is a variable.
			$hasVariable       = false;
			$conditionStart    = $startPos;
			$altConditionStart = $this->phpcsFile->findPrevious(
				$this->condition_start_tokens,
				( $hasAssignment - 1 ),
				$startPos
			);
			if ( false !== $altConditionStart ) {
				$conditionStart = $altConditionStart;
			}

			for ( $i = $hasAssignment; $i > $conditionStart; $i-- ) {
				if ( isset( Tokens::$emptyTokens[ $this->tokens[ $i ]['code'] ] ) ) {
					continue;
				}

				// If this is a variable or array, we've seen all we need to see.
				if ( T_VARIABLE === $this->tokens[ $i ]['code']
					|| T_CLOSE_SQUARE_BRACKET === $this->tokens[ $i ]['code']
				) {
					$hasVariable = true;
					break;
				}

				// If this is a function call or something, we are OK.
				if ( T_CLOSE_PARENTHESIS === $this->tokens[ $i ]['code'] ) {
					break;
				}
			}

			if ( true === $hasVariable ) {
				$this->phpcsFile->addWarning(
					'Variable assignment found within a condition. Did you mean to do a comparison ?',
					$hasAssignment,
					'Found'
				);
			}

			$startPos = $hasAssignment;

		} while ( $startPos < $closer );

	}

} // End class.
