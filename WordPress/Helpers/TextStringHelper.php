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
 * Helper utilities for handling text strings.
 *
 * ---------------------------------------------------------------------------------------------
 * This class is only intended for internal use by WordPressCS and is not part of the public API.
 * This also means that it has no promise of backward compatibility. Use at your own risk.
 * ---------------------------------------------------------------------------------------------
 *
 * {@internal The functionality in this class will likely be replaced at some point in
 * the future by functions from PHPCSUtils.}
 *
 * @package WPCS\WordPressCodingStandards
 * @since   3.0.0 The method in this class was previously contained in the
 *                `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class TextStringHelper {

	/**
	 * Get the interpolated variable names from a string.
	 *
	 * Check if '$' is followed by a valid variable name, and that it is not preceded by an escape sequence.
	 *
	 * @since 0.9.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - Visibility is now `public` (was `protected`) and the method `static`.
	 *
	 * @param string $string The contents of a T_DOUBLE_QUOTED_STRING or T_HEREDOC token.
	 *
	 * @return array Variable names (without '$' sigil).
	 */
	public static function get_interpolated_variables( $string ) {
		$variables = array();
		if ( preg_match_all( '/(?P<backslashes>\\\\*)\$(?P<symbol>\w+)/', $string, $match_sets, \PREG_SET_ORDER ) ) {
			foreach ( $match_sets as $matches ) {
				if ( ! isset( $matches['backslashes'] ) || ( \strlen( $matches['backslashes'] ) % 2 ) === 0 ) {
					$variables[] = $matches['symbol'];
				}
			}
		}
		return $variables;
	}
}
