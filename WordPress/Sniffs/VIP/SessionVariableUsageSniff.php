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
 * Discourages the use of the session variable.
 * Creating a session writes a file to the server and is unreliable in a multi-server environment.
 *
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#session_start-and-other-session-related-functions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.10.0 The sniff no longer needlessly extends the Generic_Sniffs_PHP_ForbiddenFunctionsSniff
 *                 which it didn't use.
 * @since   0.12.0 This class now extends WordPress_Sniff.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class SessionVariableUsageSniff extends Sniff {

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
		if ( '$_SESSION' === $this->tokens[ $stackPtr ]['content'] ) {
			$this->phpcsFile->addError(
				'Usage of $_SESSION variable is prohibited.',
				$stackPtr,
				'SessionVarsProhibited'
			);
		}
	}

} // End class.
