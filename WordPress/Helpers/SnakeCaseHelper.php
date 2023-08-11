<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

use PHPCSUtils\BackCompat\Helper;

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
 * @internal
 *
 * @since 3.0.0 The method in this class was previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class SnakeCaseHelper {

	/**
	 * Transform the name of a PHP construct (function, variable etc) to one in snake_case.
	 *
	 * @since 2.0.0 Moved from the `WordPress.NamingConventions.ValidFunctionName` sniff
	 *              to this class, renamed from `get_name_suggestion` and made static
	 *              so it can also be used by classes which don't extend this class.
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - Renamed from `get_snake_case_name_suggestion()` to `get_suggestion()`.
	 *
	 * @param string $name The construct name.
	 *
	 * @return string
	 */
	public static function get_suggestion( $name ) {
		$suggested = preg_replace( '`(?<!_|^)([A-Z])`', '_$1', $name );

		if ( preg_match( '`^[a-z0-9_]+$`i', $suggested ) === 1 ) {
			// If the name only contains ASCII characters, we can safely lowercase it.
			$suggested = strtolower( $suggested );
		} elseif ( function_exists( 'mb_strtolower' ) ) {
			$suggested = mb_strtolower( $suggested, Helper::getEncoding() );
		} else {
			// If the name contains non-ASCII chars and Mbstring is not available, only transliterate the ASCII chars.
			$suggested = strtr( $suggested, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz' );
		}

		return $suggested;
	}
}
