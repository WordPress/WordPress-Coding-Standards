<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\CSRF;

use WordPress\Sniff;

/**
 * Checks that nonce verification accompanies form processing.
 *
 * @link    https://developer.wordpress.org/plugins/security/nonces/ Nonces on Plugin Developer Handbook
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.5.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class NonceVerificationSniff extends Sniff {

	/**
	 * Superglobals to notify about when not accompanied by an nonce check.
	 *
	 * A value of `true` results in an error. A value of `false` in a warning.
	 *
	 * @since 0.12.0
	 *
	 * @var array
	 */
	protected $superglobals = array(
		'$_POST'    => true,
		'$_FILE'    => true,
		'$_GET'     => false,
		'$_REQUEST' => false,
	);

	/**
	 * Superglobals to give an error for when not accompanied by an nonce check.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed visibility from public to protected.
	 *
	 * @deprecated 0.12.0 Replaced by $superglobals property.
	 *
	 * @var array
	 */
	protected $errorForSuperGlobals = array();

	/**
	 * Superglobals to give a warning for when not accompanied by an nonce check.
	 *
	 * If the variable is also in the error list, that takes precedence.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed visibility from public to protected.
	 *
	 * @deprecated 0.12.0 Replaced by $superglobals property.
	 *
	 * @var array
	 */
	protected $warnForSuperGlobals = array();

	/**
	 * Custom list of functions which verify nonces.
	 *
	 * @since 0.5.0
	 *
	 * @var string|string[]
	 */
	public $customNonceVerificationFunctions = array();

	/**
	 * Custom list of functions that sanitize the values passed to them.
	 *
	 * @since 0.11.0
	 *
	 * @var string|string[]
	 */
	public $customSanitizingFunctions = array();

	/**
	 * Custom sanitizing functions that implicitly unslash the values passed to them.
	 *
	 * @since 0.11.0
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
	 * @since 0.11.0 - Changed from public static to protected non-static.
	 *               - Changed the format from simple bool to array.
	 *
	 * @var array
	 */
	protected $addedCustomFunctions = array(
		'nonce'           => null,
		'sanitize'        => null,
		'unslashsanitize' => null,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {

		return array(
			T_VARIABLE,
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

		$instance = $this->tokens[ $stackPtr ];

		if ( ! isset( $this->superglobals[ $instance['content'] ] ) ) {
			return;
		}

		if ( $this->has_whitelist_comment( 'CSRF', $stackPtr ) ) {
			return;
		}

		if ( $this->is_assignment( $stackPtr ) ) {
			return;
		}

		$this->mergeFunctionLists();

		if ( $this->is_only_sanitized( $stackPtr ) ) {
			return;
		}

		if ( $this->has_nonce_check( $stackPtr ) ) {
			return;
		}

		// If we're still here, no nonce-verification function was found.
		$this->addMessage(
			'Processing form data without nonce verification.',
			$stackPtr,
			$this->superglobals[ $instance['content'] ],
			'NoNonceVerification'
		);

	} // End process_token().

	/**
	 * Merge custom functions provided via a custom ruleset with the defaults, if we haven't already.
	 *
	 * @since 0.11.0 Split out from the `process()` method.
	 *
	 * @return void
	 */
	protected function mergeFunctionLists() {
		if ( $this->customNonceVerificationFunctions !== $this->addedCustomFunctions['nonce'] ) {
			$this->nonceVerificationFunctions = $this->merge_custom_array(
				$this->customNonceVerificationFunctions,
				$this->nonceVerificationFunctions
			);
			$this->addedCustomFunctions['nonce'] = $this->customNonceVerificationFunctions;
		}

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

} // End class.
