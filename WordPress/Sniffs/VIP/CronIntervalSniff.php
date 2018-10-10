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
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Flag cron schedules less than 15 minutes.
 *
 * @link https://vip.wordpress.com/documentation/vip-go/code-review-blockers-warnings-notices/#cron-schedules-less-than-15-minutes-or-expensive-events
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.11.0 - Extends the WordPress_Sniff class.
 *                 - Now deals correctly with WP time constants.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   0.14.0 The minimum cron interval tested against is now configurable.
 *
 * @deprecated 1.0.0  This sniff has been moved to the `WP` category.
 *                    This file remains for now to prevent BC breaks.
 */
class CronIntervalSniff extends \WordPress\Sniffs\WP\CronIntervalSniff {

	/**
	 * Minimum allowed cron interval in seconds.
	 *
	 * Defaults to 900 (= 15 minutes), which is the requirement for the VIP platform.
	 *
	 * @since 0.14.0
	 *
	 * @var int
	 */
	public $min_interval = 900;


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
				'The "WordPress.VIP.CronInterval" sniff has been renamed to "WordPress.WP.CronInterval". Please update your custom ruleset.',
				0,
				'DeprecatedSniff'
			);
		}

		if ( 900 !== (int) $this->min_interval
			&& false === $this->thrown['FoundPropertyForDeprecatedSniff']
		) {
			$this->thrown['FoundPropertyForDeprecatedSniff'] = $this->phpcsFile->addWarning(
				'The "WordPress.VIP.CronInterval" sniff has been renamed to "WordPress.WP.CronInterval". Please update your custom ruleset.',
				0,
				'FoundPropertyForDeprecatedSniff'
			);
		}

		return parent::process_token( $stackPtr );
	}

}
