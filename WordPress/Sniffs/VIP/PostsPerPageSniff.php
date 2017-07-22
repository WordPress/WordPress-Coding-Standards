<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\VIP;

use WordPress\AbstractArrayAssignmentRestrictionsSniff;

/**
 * Flag returning high or infinite posts_per_page.
 *
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#no-limit-queries
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class PostsPerPageSniff extends AbstractArrayAssignmentRestrictionsSniff {

	/**
	 * Groups of variables to restrict.
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			'posts_per_page' => array(
				'type' => 'error',
				'keys' => array(
					'posts_per_page',
					'nopaging',
					'numberposts',
				),
			),
		);
	}

	/**
	 * Callback to process each confirmed key, to check value.
	 * This must be extended to add the logic to check assignment value.
	 *
	 * @param  string $key   Array index / key.
	 * @param  mixed  $val   Assigned value.
	 * @param  int    $line  Token line.
	 * @param  array  $group Group definition.
	 * @return mixed         FALSE if no match, TRUE if matches, STRING if matches
	 *                       with custom error message passed to ->process().
	 */
	public function callback( $key, $val, $line, $group ) {
		$key = strtolower( $key );
		if (
			( 'nopaging' === $key && ( 'true' === $val || 1 === $val ) )
			||
			( in_array( $key, array( 'numberposts', 'posts_per_page' ), true ) && '-1' == $val )
			) {

			return 'Disabling pagination is prohibited in VIP context, do not set `%s` to `%s` ever.';

		} elseif ( in_array( $key, array( 'posts_per_page', 'numberposts' ), true ) ) {

			if ( $val > 100 ) {
				return 'Detected high pagination limit, `%s` is set to `%s`';
			}
		}
	}

} // End class.
