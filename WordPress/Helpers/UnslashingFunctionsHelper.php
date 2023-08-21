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
 * Helper functions and function lists for checking whether a function is an unslashing function.
 *
 * @since 3.0.0 The property in this class was previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class UnslashingFunctionsHelper {

	/**
	 * Functions which unslash the data passed to them.
	 *
	 * @since 2.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - Visibility changed from protected to private and property made static.
	 *                Use the `get_functions()` method for access.
	 *
	 * @var array<string, bool>
	 */
	private static $unslashingFunctions = array(
		'stripslashes_deep'              => true,
		'stripslashes_from_strings_only' => true,
		'wp_unslash'                     => true,
	);

	/**
	 * Retrieve a list of the unslashing functions.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, bool>
	 */
	public static function get_functions() {
		return self::$unslashingFunctions;
	}

	/**
	 * Check if a particular function is regarded as a unslashing function.
	 *
	 * @since 3.0.0
	 *
	 * @param string $functionName The name of the function to check.
	 *
	 * @return bool
	 */
	public static function is_unslashing_function( $functionName ) {
		return isset( self::$unslashingFunctions[ strtolower( $functionName ) ] );
	}
}
