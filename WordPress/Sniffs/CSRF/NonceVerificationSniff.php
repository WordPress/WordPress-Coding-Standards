<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Checks that nonce verification accompanies form processing.
 *
 * @link    https://developer.wordpress.org/plugins/security/nonces/ Nonces on Plugin Developer Handbook
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.5.0
 */
class WordPress_Sniffs_CSRF_NonceVerificationSniff extends WordPress_Sniff {

	/**
	 * Superglobals to give an error for when not accompanied by an nonce check.
	 *
	 * @since 0.5.0
	 *
	 * @var array
	 */
	public $errorForSuperGlobals = array( '$_POST', '$_FILE' );

	/**
	 * Superglobals to give a warning for when not accompanied by an nonce check.
	 *
	 * If the variable is also in the error list, that takes precedence.
	 *
	 * @since 0.5.0
	 *
	 * @var array
	 */
	public $warnForSuperGlobals = array( '$_GET', '$_REQUEST' );

	/**
	 * Custom list of functions which verify nonces.
	 *
	 * @since 0.5.0
	 *
	 * @var array
	 */
	public $customNonceVerificationFunctions = array();

	/**
	 * Whether the custom functions have been added to the default list yet.
	 *
	 * @since 0.5.0
	 *
	 * @var bool
	 */
	public static $addedCustomFunctions = false;

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
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

		// Merge any custom functions with the defaults, if we haven't already.
		if ( ! self::$addedCustomFunctions ) {
			self::$nonceVerificationFunctions = array_merge(
				self::$nonceVerificationFunctions
				, array_flip( $this->customNonceVerificationFunctions )
			);

			self::$addedCustomFunctions = true;
		}

		$this->init( $phpcsFile );

		$instance = $this->tokens[ $stackPtr ];

		$superglobals = array_merge(
			$this->errorForSuperGlobals
			, $this->warnForSuperGlobals
		);

		if ( ! in_array( $instance['content'], $superglobals, true ) ) {
			return;
		}

		if ( $this->has_whitelist_comment( 'CSRF', $stackPtr ) ) {
			return;
		}

		if ( $this->is_assignment( $stackPtr ) ) {
			return;
		}

		if ( $this->is_only_sanitized( $stackPtr ) ) {
			return;
		}

		if ( $this->has_nonce_check( $stackPtr ) ) {
			return;
		}

		// If we're still here, no nonce-verification function was found.
		$severity = 'warning';
		if ( in_array( $instance['content'], $this->errorForSuperGlobals, true ) ) {
			$severity = 0;
		}

		$phpcsFile->addError(
			'Processing form data without nonce verification.'
			, $stackPtr
			, 'NoNonceVerification'
			, array()
			, $severity
		);

	} // End process().

} // End class.
