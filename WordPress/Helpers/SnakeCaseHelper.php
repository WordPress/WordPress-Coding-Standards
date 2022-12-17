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
 * Helper utilities for checking if a name is in snake_case.
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
final class SnakeCaseHelper {

	/**
	 * Transform the name of a PHP construct (function, variable etc) to one in snake_case.
	 *
	 * @since 2.0.0 Moved from the `WordPress.NamingConventions.ValidFunctionName` sniff
	 *              to this class, renamed from `get_name_suggestion` and made static
	 *              so it can also be used by classes which don't extend this class.
	 * @since 3.0.0 Moved from the Sniff class to this class.
	 *
	 * @param string $name The construct name.
	 *
	 * @return string
	 */
	public static function get_suggestion( $name ) {
		$suggested = preg_replace( '`([A-Z])`', '_$1', $name );
		$suggested = strtolower( $suggested );
		$suggested = str_replace( '__', '_', $suggested );
		$suggested = trim( $suggested, '_' );
		return $suggested;
	}
}
