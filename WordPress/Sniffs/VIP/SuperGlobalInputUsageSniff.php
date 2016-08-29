<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Flag any usage of super global input var ( _GET / _POST / etc. ).
 *
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/79
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.4.0 This class now extends WordPress_Sniff.
 */
class WordPress_Sniffs_VIP_SuperGlobalInputUsageSniff extends WordPress_Sniff {

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
		$this->init( $phpcsFile );

		// Check for global input variable.
		if ( ! in_array( $this->tokens[ $stackPtr ]['content'], self::$input_superglobals, true ) ) {
			return;
		}

		$varName = $this->tokens[ $stackPtr ]['content'];

		// If we're overriding a superglobal with an assignment, no need to test.
		if ( $this->is_assignment( $stackPtr ) ) {
			return;
		}

		// Check for whitelisting comment.
		if ( ! $this->has_whitelist_comment( 'input var', $stackPtr ) ) {
			$phpcsFile->addWarning( 'Detected access of super global var %s, probably needs manual inspection.', $stackPtr, 'AccessDetected', array( $varName ) );
		}

	}

} // End class.
