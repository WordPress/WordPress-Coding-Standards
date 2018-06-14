<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\Variables;

use WordPress\Sniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Warns about overwriting WordPress native global variables.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.4.0  This class now extends WordPress_Sniff.
 * @since   0.12.0 The $wp_globals property has been moved to the WordPress_Sniff.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * @deprecated 1.0.0  This sniff has been moved to the `WP` category.
 *                    This file remains for now to prevent BC breaks.
 *
 * @uses \WordPress\Sniff::$custom_test_class_whitelist
 */
class GlobalVariablesSniff extends \WordPress\Sniffs\WP\GlobalVariablesOverrideSniff {

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
				'The "WordPress.Variables.GlobalVariables" sniff has been renamed to "WordPress.WP.GlobalVariablesOverride". Please update your custom ruleset.',
				0,
				'DeprecatedSniff'
			);
		}

		if ( ! empty( $this->custom_test_class_whitelist )
			&& false === $this->thrown['FoundPropertyForDeprecatedSniff']
		) {
			$this->thrown['FoundPropertyForDeprecatedSniff'] = $this->phpcsFile->addWarning(
				'The "WordPress.Variables.GlobalVariables" sniff has been renamed to "WordPress.WP.GlobalVariablesOverride". Please update your custom ruleset.',
				0,
				'FoundPropertyForDeprecatedSniff'
			);
		}

		return parent::process_token( $stackPtr );
	}

}
