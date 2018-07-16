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
 *
 * @deprecated 1.0.0  This sniff has been deprecated.
 *                    This file remains for now to prevent BC breaks.
 */
class SuperGlobalInputUsageSniff extends Sniff {

	/**
	 * Keep track of whether the warnings have been thrown to prevent
	 * the messages being thrown for every token triggering the sniff.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $thrown = array(
		'DeprecatedSniff' => false,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			\T_VARIABLE,
		);
	}

	/**
	 * Process the token and handle the deprecation notice.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {
		if ( false === $this->thrown['DeprecatedSniff'] ) {
			$this->thrown['DeprecatedSniff'] = $this->phpcsFile->addWarning(
				'The "WordPress.VIP.SuperGlobalInputUsage" sniff has been deprecated. Please update your custom ruleset.',
				0,
				'DeprecatedSniff'
			);
		}

		// Check for global input variable.
		if ( ! \in_array( $this->tokens[ $stackPtr ]['content'], $this->input_superglobals, true ) ) {
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

}
