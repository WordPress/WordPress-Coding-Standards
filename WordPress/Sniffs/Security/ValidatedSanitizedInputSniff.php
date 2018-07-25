<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\Security;

use WordPress\Sniff;

/**
 * Flag any non-validated/sanitized input ( _GET / _POST / etc. ).
 *
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/69
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.4.0  This class now extends WordPress_Sniff.
 * @since   0.5.0  Method getArrayIndexKey() has been moved to WordPress_Sniff.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  This sniff has been moved from the `VIP` category to the `Security` category.
 */
class ValidatedSanitizedInputSniff extends Sniff {

	/**
	 * Check for validation functions for a variable within its own parenthesis only.
	 *
	 * @var boolean
	 */
	public $check_validation_in_scope_only = false;

	/**
	 * Custom list of functions that sanitize the values passed to them.
	 *
	 * @since 0.5.0
	 *
	 * @var string|string[]
	 */
	public $customSanitizingFunctions = array();

	/**
	 * Custom sanitizing functions that implicitly unslash the values passed to them.
	 *
	 * @since 0.5.0
	 *
	 * @var string|string[]
	 */
	public $customUnslashingSanitizingFunctions = array();

	/**
	 * Cache of previously added custom functions.
	 *
	 * Prevents having to do the same merges over and over again.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 - Changed from static to non-static.
	 *               - Changed the format from simple bool to array.
	 *
	 * @var array
	 */
	protected $addedCustomFunctions = array(
		'sanitize'        => array(),
		'unslashsanitize' => array(),
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			\T_VARIABLE,
			\T_DOUBLE_QUOTED_STRING,
			\T_HEREDOC,
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

		$superglobals = $this->input_superglobals;

		// Handling string interpolation.
		if ( \T_DOUBLE_QUOTED_STRING === $this->tokens[ $stackPtr ]['code']
			|| \T_HEREDOC === $this->tokens[ $stackPtr ]['code']
		) {
			$interpolated_variables = array_map(
				function ( $symbol ) {
					return '$' . $symbol;
				},
				$this->get_interpolated_variables( $this->tokens[ $stackPtr ]['content'] )
			);
			foreach ( array_intersect( $interpolated_variables, $superglobals ) as $bad_variable ) {
				$this->phpcsFile->addError( 'Detected usage of a non-sanitized, non-validated input variable %s: %s', $stackPtr, 'InputNotValidatedNotSanitized', array( $bad_variable, $this->tokens[ $stackPtr ]['content'] ) );
			}

			return;
		}

		// Check if this is a superglobal.
		if ( ! \in_array( $this->tokens[ $stackPtr ]['content'], $superglobals, true ) ) {
			return;
		}

		// If we're overriding a superglobal with an assignment, no need to test.
		if ( $this->is_assignment( $stackPtr ) ) {
			return;
		}

		// This superglobal is being validated.
		if ( $this->is_in_isset_or_empty( $stackPtr ) ) {
			return;
		}

		$array_key = $this->get_array_access_key( $stackPtr );

		if ( empty( $array_key ) ) {
			return;
		}

		$error_data = array( $this->tokens[ $stackPtr ]['content'] );

		// Check for validation first.
		if ( ! $this->is_validated( $stackPtr, $array_key, $this->check_validation_in_scope_only ) ) {
			$this->phpcsFile->addError( 'Detected usage of a non-validated input variable: %s', $stackPtr, 'InputNotValidated', $error_data );
			// return; // Should we just return and not look for sanitizing functions ?
		}

		if ( $this->has_whitelist_comment( 'sanitization', $stackPtr ) ) {
			return;
		}

		// If this is a comparison ('a' == $_POST['foo']), sanitization isn't needed.
		if ( $this->is_comparison( $stackPtr ) ) {
			return;
		}

		$this->mergeFunctionLists();

		// Now look for sanitizing functions.
		if ( ! $this->is_sanitized( $stackPtr, true ) ) {
			$this->phpcsFile->addError( 'Detected usage of a non-sanitized input variable: %s', $stackPtr, 'InputNotSanitized', $error_data );
		}
	}

	/**
	 * Merge custom functions provided via a custom ruleset with the defaults, if we haven't already.
	 *
	 * @since 0.11.0 Split out from the `process()` method.
	 *
	 * @return void
	 */
	protected function mergeFunctionLists() {
		if ( $this->customSanitizingFunctions !== $this->addedCustomFunctions['sanitize'] ) {
			$this->sanitizingFunctions = $this->merge_custom_array(
				$this->customSanitizingFunctions,
				$this->sanitizingFunctions
			);

			$this->addedCustomFunctions['sanitize'] = $this->customSanitizingFunctions;
		}

		if ( $this->customUnslashingSanitizingFunctions !== $this->addedCustomFunctions['unslashsanitize'] ) {
			$this->unslashingSanitizingFunctions = $this->merge_custom_array(
				$this->customUnslashingSanitizingFunctions,
				$this->unslashingSanitizingFunctions
			);

			$this->addedCustomFunctions['unslashsanitize'] = $this->customUnslashingSanitizingFunctions;
		}
	}

}
