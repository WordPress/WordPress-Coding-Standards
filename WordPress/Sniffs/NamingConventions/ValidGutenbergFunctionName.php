<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\NamingConventions;

use WordPressCS\WordPress\Sniff;

/**
 * Enforces WordPress function name and method name format, based upon Squiz code.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.1.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   2.0.0  The `get_name_suggestion()` method has been moved to the
 *                 WordPress native `Sniff` base class as `get_snake_case_name_suggestion()`.
 * @since   2.2.0  Will now ignore functions and methods which are marked as @deprecated.
 * @since   3.0.0  This sniff has been refactored and no longer extends the upstream
 *                 PEAR.NamingConventions.ValidFunctionName sniff.
 */
class ValidGutenbergFunctionNameSniff extends Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 * @since 3.0.0
	 *
	 */
	public function register() {
		return array( \T_FUNCTION );
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 * @since 3.0.0
	 *
	 */
	public function process_token( $stackPtr ) {
		$functionName = $this->phpcsFile->getDeclarationName( $stackPtr );

		if ( preg_match( '/^_?gutenberg_[^\s]+$/', $functionName ) ) {
			// The function has a valid prefix.
			return null;
		}


		$error      = 'The function should have a gutenberg prefix.';
		$error_code = 'IncorrectFunctionPrefix';

		// Don't auto-fix: Something other than whitespace found between keyword and open parenthesis.
		$this->phpcsFile->addError( $error, $stackPtr, $error_code );
	}

}
