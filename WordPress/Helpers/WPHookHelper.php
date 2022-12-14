<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

/**
 * Helper utilities for recognizing functions related to the WP Hook mechanism.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   3.0.0 The property in this class was previously contained in the
 *                `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class WPHookHelper {

	/**
	 * A list of functions that invoke WP hooks (filters/actions).
	 *
	 * @since 0.10.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0  - Moved from the Sniff class to this class.
	 *               - The property visibility has changed from `protected` to `private static`.
	 *                 Use the `get_function_names()` method for access.
	 *
	 * @var array<string, bool>
	 */
	private static $hookInvokeFunctions = array(
		'do_action'                => true,
		'do_action_ref_array'      => true,
		'do_action_deprecated'     => true,
		'apply_filters'            => true,
		'apply_filters_ref_array'  => true,
		'apply_filters_deprecated' => true,
	);

	/**
	 * Retrieve a list of the WordPress functions which invoke hooks.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $include_deprecated Whether to include the names of functions
	 *                                 which are used to invoke deprecated hooks.
	 *                                 Defaults to `true`.
	 *
	 * @return array<string, bool> Array with the function names as keys. The value is irrelevant.
	 */
	public static function get_functions( $include_deprecated = true ) {
		$hooks = self::$hookInvokeFunctions;
		if ( false === $include_deprecated ) {
			unset(
				$hooks['do_action_deprecated'],
				$hooks['apply_filters_deprecated']
			);
		}

		return $hooks;
	}
}
