<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\PHP;

use WordPressCS\WordPress\Sniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Discourage the usage of the unneeded ternary operator.
 *
 * This sniff is influenced by the same rule set in the ESLint rules.
 * When we want to store a boolean value in a variable, using a ternary operator
 * is not needed, because a test will return a boolean value.
 *
 * @link https://eslint.org/docs/rules/no-unneeded-ternary
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   2.2.0
 */
class NoUnneededTernarySniff extends Sniff {

	/**
	 * Boolean tokens array.
	 *
	 * @since   2.2.0
	 *
	 * @var array
	 */
	private $boolean_tokens = array(
		'T_TRUE'  => true,
		'T_FALSE' => true,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 2.2.0
	 *
	 * @return array
	 */
	public function register() {
		return array( \T_INLINE_THEN );
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 2.2.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 */
	public function process_token( $stackPtr ) {
		$first_token      = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );
		$end_of_statement = $this->phpcsFile->findNext( array( \T_SEMICOLON, \T_CLOSE_TAG ), $stackPtr );
		$last_token       = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $end_of_statement - 1 ), null, true );

		$first_token_type = $this->tokens[ $first_token ]['type'];
		$last_token_type  = $this->tokens[ $last_token ]['type'];

		if ( isset( $this->boolean_tokens[ $first_token_type ] ) && isset( $this->boolean_tokens[ $last_token_type ] ) ) {
			$this->phpcsFile->addError(
				'Don\'t use ternary epression to store a boolean value in a variable.',
				$stackPtr,
				'Detected'
			);
		}
	}

}
