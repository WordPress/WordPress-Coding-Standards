<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Suppress Filters need to be 'true' when getting posts using get_posts.
 *
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 */
class WordPress_Sniffs_VIP_SuppressFiltersSniff extends WordPress_AbstractArrayAssignmentRestrictionsSniff {

	/**
	 * Groups of variables to restrict.
	 * This should be overridden in extending classes.
	 *
	 * Example: groups => array(
	 * 	'wpdb' => array(
	 * 		'type'          => 'error' | 'warning',
	 * 		'message'       => 'Dont use this one please!',
	 * 		'variables'     => array( '$val', '$var' ),
	 * 		'object_vars'   => array( '$foo->bar', .. ),
	 * 		'array_members' => array( '$foo['bar']', .. ),
	 * 	)
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			'suppress_filters' => array(
				'type' => 'warning',
				'keys' => array(
					'suppress_filters',
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
		if ( ( 'suppress_filters' === $key && ( 'true' === $val || 1 === $val ) ) ) {
			return 'get_posts() is discouraged in favor of creating a new WP_Query() so that Advanced Post Cache will cache the query, unless you explicitly supply suppress_filters => false.';
		} else {
			return false;
		}
	}

} // End class.
