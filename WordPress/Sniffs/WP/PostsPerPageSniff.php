<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use PHPCSUtils\Utils\Numbers;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractArrayAssignmentRestrictionsSniff;

/**
 * Flag returning high or infinite posts_per_page.
 *
 * @link https://vip.wordpress.com/documentation/vip-go/code-review-blockers-warnings-notices/#no-limit-queries
 *
 * @since 0.3.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 0.14.0 Added the posts_per_page property.
 * @since 1.0.0  This sniff has been split into two, with the check for high pagination
 *               limit being part of the WP category, and the check for pagination
 *               disabling being part of the VIP category.
 */
final class PostsPerPageSniff extends AbstractArrayAssignmentRestrictionsSniff {

	/**
	 * Posts per page limit to check against.
	 *
	 * @since 0.14.0
	 *
	 * @var int
	 */
	public $posts_per_page = 100;

	/**
	 * Groups of variables to restrict.
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			'posts_per_page' => array(
				'type'    => 'warning',
				'message' => 'Detected high pagination limit, `%s` is set to `%s`',
				'keys'    => array(
					'posts_per_page',
					'numberposts',
				),
			),
		);
	}

	/**
	 * Callback to process each confirmed key, to check value.
	 *
	 * @param  string $key   Array index / key.
	 * @param  mixed  $val   Assigned value.
	 * @param  int    $line  Token line.
	 * @param  array  $group Group definition.
	 *
	 * @return bool FALSE if no match, TRUE if matches.
	 */
	public function callback( $key, $val, $line, $group ) {
		$stripped_val = TextStrings::stripQuotes( $val );

		if ( $val !== $stripped_val ) {
			// The value was a text string. For text strings, we only accept purely numeric values.
			if ( preg_match( '`^[0-9]+$`', $stripped_val ) !== 1 ) {
				// Not a purely numeric value, so any comparison would be a false comparison.
				return false;
			}

			// Purely numeric string, treat it as an integer from here on out.
			$val = $stripped_val;
		}

		$first_char = $val[0];
		if ( '-' === $first_char || '+' === $first_char ) {
			$val = ltrim( $val, '-+' );
		} else {
			$first_char = '';
		}

		$real_value = Numbers::getDecimalValue( $val );
		if ( false === $real_value ) {
			// This wasn't a purely numeric value, so any comparison would be a false comparison.
			return false;
		}

		$val = $first_char . $real_value;

		return ( (int) $val > (int) $this->posts_per_page );
	}
}
