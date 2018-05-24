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
 *
 * @deprecated 1.0.0  This sniff has been moved to the `Security` category.
 *                    This file remains for now to prevent BC breaks.
 */
class NonceVerificationSniff extends \WordPress\Sniffs\Security\NonceVerificationSniff {

	/**
	 * Keep track of whether the warnings have been thrown to prevent
	 * the messages being thrown for every token triggering the sniff.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $thrown = array(
		'DeprecatedSniff'                 => false,
		'FoundPropertyForDeprecatedSniff' => false,
	);

	/**
	 * Don't use.
	 *
	 * @deprecated 1.0.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void|int
	 */
	public function process_token( $stackPtr ) {
		if ( false === $this->thrown['DeprecatedSniff'] ) {
			$this->phpcsFile->addWarning(
				'The "WordPress.CSRF.NonceVerification" sniff has been renamed to "WordPress.Security.NonceVerification". Please update your custom ruleset.',
				0,
				'DeprecatedSniff'
			);

			$this->thrown['DeprecatedSniff'] = true;
		}

		if ( ( $this->customNonceVerificationFunctions !== $this->addedCustomFunctions['nonce']
			|| $this->customSanitizingFunctions !== $this->addedCustomFunctions['sanitize']
			|| $this->customUnslashingSanitizingFunctions !== $this->addedCustomFunctions['unslashsanitize'] )
			&& false === $this->thrown['FoundPropertyForDeprecatedSniff']
		) {
			$this->phpcsFile->addWarning(
				'The "WordPress.CSRF.NonceVerification" sniff has been renamed to "WordPress.Security.NonceVerification". Please update your custom ruleset.',
				0,
				'FoundPropertyForDeprecatedSniff'
			);

			$this->thrown['FoundPropertyForDeprecatedSniff'] = true;
		}

		return parent::process_token( $stackPtr );
	}

}
