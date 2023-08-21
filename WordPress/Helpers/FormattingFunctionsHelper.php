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
 * Helper functions and function lists for checking whether a function is a formatting function.
 *
 * @since 3.0.0 The property in this class was previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class FormattingFunctionsHelper {

	/**
	 * Functions that format strings.
	 *
	 * These functions are often used for formatting values just before output, and
	 * it is common practice to escape the individual parameters passed to them as
	 * needed instead of escaping the entire result. This is especially true when the
	 * string being formatted contains HTML, which makes escaping the full result
	 * more difficult.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - Visibility changed from protected to private and property made static.
	 *
	 * @var array<string, bool>
	 */
	private static $formattingFunctions = array(
		'antispambot' => true,
		'array_fill'  => true,
		'ent2ncr'     => true,
		'implode'     => true,
		'join'        => true,
		'nl2br'       => true,
		'sprintf'     => true,
		'vsprintf'    => true,
		'wp_sprintf'  => true,
	);

	/**
	 * Check if a particular function is regarded as a formatting function.
	 *
	 * @since 3.0.0
	 *
	 * @param string $functionName The name of the function to check.
	 *
	 * @return bool
	 */
	public static function is_formatting_function( $functionName ) {
		return isset( self::$formattingFunctions[ strtolower( $functionName ) ] );
	}
}
