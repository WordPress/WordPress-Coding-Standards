<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\VIP;

use WordPress\AbstractFunctionParameterSniff;

/**
 * Warn about __FILE__ for page registration.
 *
 * @link https://vip.wordpress.com/documentation/vip-go/code-review-blockers-warnings-notices/#using-__file__-for-page-registration
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.11.0 Refactored to extend the new WordPress_AbstractFunctionParameterSniff.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * @deprecated 1.0.0  This sniff has been moved to the `Security` category.
 *                    This file remains for now to prevent BC breaks.
 */
class PluginMenuSlugSniff extends \WordPress\Sniffs\Security\PluginMenuSlugSniff {

	/**
	 * Keep track of whether the warning has been thrown to prevent
	 * the message being thrown for every token triggering the sniff.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $thrown = array(
		'DeprecatedSniff' => false,
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
				'The "WordPress.VIP.PluginMenuSlug" sniff has been renamed to "WordPress.Security.PluginMenuSlug". Please update your custom ruleset.',
				0,
				'DeprecatedSniff'
			);

			$this->thrown['DeprecatedSniff'] = true;
		}

		return parent::process_token( $stackPtr );
	}

}
