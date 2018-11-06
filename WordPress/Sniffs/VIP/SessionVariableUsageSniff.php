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
 * @link https://lobby.vip.wordpress.com/wordpress-com-documentation/code-review-what-we-look-for/#session-start-and-other-session-functions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.10.0 The sniff no longer needlessly extends the Generic_Sniffs_PHP_ForbiddenFunctionsSniff
 *                 which it didn't use.
 * @since   0.12.0 This class now extends WordPress_Sniff.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * @deprecated 1.0.0  This sniff has been deprecated.
 *                    This file remains for now to prevent BC breaks.
 */
class SessionVariableUsageSniff extends Sniff {

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
				'The "WordPress.VIP.SessionVariableUsage" sniff has been deprecated. Please update your custom ruleset.',
				0,
				'DeprecatedSniff'
			);
		}

		if ( '$_SESSION' === $this->tokens[ $stackPtr ]['content'] ) {
			$this->phpcsFile->addError(
				'Usage of $_SESSION variable is prohibited.',
				$stackPtr,
				'SessionVarsProhibited'
			);
		}
	}

}
