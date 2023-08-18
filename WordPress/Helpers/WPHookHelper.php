<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

use PHPCSUtils\Utils\PassedParameters;

/**
 * Helper utilities for recognizing functions related to the WP Hook mechanism.
 *
 * @since 3.0.0 The property in this class was previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class WPHookHelper {

	/**
	 * A list of functions that invoke WP hooks (filters/actions).
	 *
	 * @since 0.10.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0  - Moved from the Sniff class to this class.
	 *               - The property visibility has changed from `protected` to `private static`.
	 *                 Use the `get_functions()` method for access.
	 *               - The format of the value has changed from a non-relevant boolean to
	 *                 an array with the parameter position and name(s) for the hook name parameter.
	 *
	 * @var array<string, array<string, int|string|string[]>> Function name as key, array with target
	 *                                                        parameter position and name(s) as value.
	 */
	private static $hookInvokeFunctions = array(
		'do_action' => array(
			'position' => 1,
			'name'     => 'hook_name',
		),
		'do_action_ref_array' => array(
			'position' => 1,
			'name'     => 'hook_name',
		),
		'do_action_deprecated' => array(
			'position' => 1,
			'name'     => 'hook_name',
		),
		'apply_filters' => array(
			'position' => 1,
			'name'     => 'hook_name',
		),
		'apply_filters_ref_array' => array(
			'position' => 1,
			'name'     => 'hook_name',
		),
		'apply_filters_deprecated' => array(
			'position' => 1,
			'name'     => 'hook_name',
		),
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
		$hooks = array_fill_keys( array_keys( self::$hookInvokeFunctions ), true );
		if ( false === $include_deprecated ) {
			unset(
				$hooks['do_action_deprecated'],
				$hooks['apply_filters_deprecated']
			);
		}

		return $hooks;
	}

	/**
	 * Retrieve the parameter information for the hook name parameter from a stack of parameters
	 * passed to one of the WP hook functions.
	 *
	 * @since 3.0.0
	 *
	 * @param string $function_name The name of the WP hook function which the parameters were passed to.
	 * @param array  $parameters    The output of a previous call to PassedParameters::getParameters().
	 *
	 * @return array|false Array with information on the parameter at the specified offset,
	 *                     or with the specified name.
	 *                     Or `FALSE` if the specified parameter is not found.
	 *                     See the PHPCSUtils PassedParameters::getParameters() documentation
	 *                     for the format of the returned (single-dimensional) array.
	 */
	public static function get_hook_name_param( $function_name, array $parameters ) {
		$function_lc = strtolower( $function_name );
		if ( isset( self::$hookInvokeFunctions[ $function_lc ] ) === false ) {
			return false;
		}

		return PassedParameters::getParameterFromStack(
			$parameters,
			self::$hookInvokeFunctions[ $function_lc ]['position'],
			self::$hookInvokeFunctions[ $function_lc ]['name']
		);
	}
}
