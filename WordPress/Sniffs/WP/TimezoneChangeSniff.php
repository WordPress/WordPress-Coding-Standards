<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use WordPressCS\WordPress\AbstractFunctionRestrictionsSniff;

/**
 * Disallow changing the timezone and use of selective date/time functions which lead to
 * timezone related bugs.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.11.0 Extends the WordPressCS native `AbstractFunctionRestrictionsSniff`
 *                 class instead of the upstream `Generic.PHP.ForbiddenFunctions` sniff.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  This sniff has been moved from the `VIP` category to the `WP` category.
 * @since   2.2.0  New group `date` added.
 */
class TimezoneChangeSniff extends AbstractFunctionRestrictionsSniff {

	/**
	 * Groups of functions to restrict.
	 *
	 * Example: groups => array(
	 *  'lambda' => array(
	 *      'type'      => 'error' | 'warning',
	 *      'message'   => 'Use anonymous functions instead please!',
	 *      'functions' => array( 'file_get_contents', 'create_function' ),
	 *  )
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(

			/*
			 * Don't change the PHP time zone.
			 *
			 * @link https://vip.wordpress.com/documentation/vip-go/code-review-blockers-warnings-notices/#manipulating-the-timezone-server-side
			 */
			'timezone_change' => array(
				'type'      => 'error',
				'message'   => 'Using %s() and similar isn\'t allowed, instead use WP internal timezone support.',
				'functions' => array(
					'date_default_timezone_set',
				),
			),

			/*
			 * Use gmdate(), not date().
			 *
			 * @link https://core.trac.wordpress.org/ticket/46438
			 * @link https://github.com/WordPress/WordPress-Coding-Standards/issues/1713
			 */
			'date' => array(
				'type'      => 'error',
				'message'   => '%s() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead.',
				'functions' => array(
					'date',
				),
			),
		);
	}

}
