<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\VIP;

use WordPress\Sniff;

/**
 * Flag any usage of super global input var ( _GET / _POST / etc. ).
 *
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/79
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.4.0  This class now extends WordPress_Sniff.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class SuperGlobalInputUsageSniff extends Sniff {

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
		// Check for global input variable.
		if ( ! in_array( $this->tokens[ $stackPtr ]['content'], $this->input_superglobals, true ) ) {
			return;
		}

		$varName = $this->tokens[ $stackPtr ]['content'];

		// If we're overriding a superglobal with an assignment, no need to test.
		if ( $this->is_assignment( $stackPtr ) ) {
			return;
		}

		// Check for whitelisting comment.
		if ( ! $this->has_whitelist_comment( 'input var', $stackPtr ) ) {
			$this->phpcsFile->addWarning( 'Detected access of super global var %s, probably needs manual inspection.', $stackPtr, 'AccessDetected', array( $varName ) );
		}

	}

} // End class.
