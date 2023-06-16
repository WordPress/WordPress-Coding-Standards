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
 * Helper functions and function lists for checking whether a function applies a callback to an array.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   3.0.0 The property in this class was previously contained in the
 *                `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class ArrayWalkingFunctionsHelper {

	/**
	 * List of array functions which apply a callback to the array.
	 *
	 * These are often used for sanitization/escaping an array variable.
	 *
	 * Note: functions which alter the array by reference are not listed here on purpose.
	 * These cannot easily be used for sanitization as they can't be combined with unslashing.
	 * Similarly, they cannot be used for late escaping as the return value is a boolean, not
	 * the altered array.
	 *
	 * @since 2.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - Visibility changed from protected to private and property made static.
	 *
	 * @var array <string function name> => <int parameter position of the callback parameter>
	 */
	private static $arrayWalkingFunctions = array(
		'array_map' => 1,
		'map_deep'  => 2,
	);

	/**
	 * Retrieve a list of the supported "array walking" functions.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, int>
	 */
	public static function get_array_walking_functions() {
		return self::$arrayWalkingFunctions;
	}

	/**
	 * Check if a particular function is an "array walking" function.
	 *
	 * @since 3.0.0
	 *
	 * @param string $functionName The name of the function to check.
	 *
	 * @return bool
	 */
	public static function is_array_walking_function( $functionName ) {
		return isset( self::$arrayWalkingFunctions[ $functionName ] );
	}
}
