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
 * {@internal The functionality in this class will likely be replaced at some point in
 * the future by functions from PHPCSUtils.}
 *
 * @package WPCS\WordPressCodingStandards
 * @since   3.0.0 The constant and methods in this class were previously contained in the
 *                `WordPressCS\WordPress\Sniff` class and have been moved here.
 */
final class TextStringHelper {

	/**
	 * Regex to get complex variables from T_DOUBLE_QUOTED_STRING or T_HEREDOC.
	 *
	 * @since 0.14.0
	 * @since 3.0.0  Moved from the Sniff class to this class.
	 *
	 * @var string
	 */
	const REGEX_COMPLEX_VARS = '`(?:(\{)?(?<!\\\\)\$)?(\{)?(?<!\\\\)\$(\{)?(?P<varname>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(?:->\$?(?P>varname)|\[[^\]]+\]|::\$?(?P>varname)|\([^\)]*\))*(?(3)\}|)(?(2)\}|)(?(1)\}|)`';

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

	/**
	 * Strip variables from an arbitrary double quoted/heredoc string.
	 *
	 * Intended for use with the contents of a T_DOUBLE_QUOTED_STRING or T_HEREDOC token.
	 *
	 * @since 0.14.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method was made `static`.
	 *
	 * @param string $string The raw string.
	 *
	 * @return string String without variables in it.
	 */
	public static function strip_interpolated_variables( $string ) {
		if ( strpos( $string, '$' ) === false ) {
			return $string;
		}

		return preg_replace( self::REGEX_COMPLEX_VARS, '', $string );
	}
}
