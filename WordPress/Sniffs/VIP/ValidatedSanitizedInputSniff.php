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
 *
 * @deprecated 1.0.0  This sniff has been moved to the `Security` category.
 *                    This file remains for now to prevent BC breaks.
 */
class ValidatedSanitizedInputSniff extends \WordPress\Sniffs\Security\ValidatedSanitizedInputSniff {

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
			$this->thrown['DeprecatedSniff'] = $this->phpcsFile->addWarning(
				'The "WordPress.VIP.ValidatedSanitizedInput" sniff has been renamed to "WordPress.Security.ValidatedSanitizedInput". Please update your custom ruleset.',
				0,
				'DeprecatedSniff'
			);
		}

		if ( false === $this->thrown['FoundPropertyForDeprecatedSniff']
			&& ( ( array() !== $this->customSanitizingFunctions && $this->customSanitizingFunctions !== $this->addedCustomFunctions['sanitize'] )
			|| ( array() !== $this->customUnslashingSanitizingFunctions && $this->customUnslashingSanitizingFunctions !== $this->addedCustomFunctions['unslashsanitize'] )
			|| false !== $this->check_validation_in_scope_only )
		) {
			$this->thrown['FoundPropertyForDeprecatedSniff'] = $this->phpcsFile->addWarning(
				'The "WordPress.VIP.ValidatedSanitizedInput" sniff has been renamed to "WordPress.Security.ValidatedSanitizedInput". Please update your custom ruleset.',
				0,
				'FoundPropertyForDeprecatedSniff'
			);
		}

		return parent::process_token( $stackPtr );
	}

}
