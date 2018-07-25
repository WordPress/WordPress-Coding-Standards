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
 * @since   0.14.0 Added the posts_per_page property.
 * @since   1.0.0  This sniff has been split into two, with the check for high pagination
 *                 limit being part of the WP category, and the check for pagination
 *                 disabling being part of the VIP category.
 */
class PostsPerPageSniff extends AbstractArrayAssignmentRestrictionsSniff {

	/**
	 * Posts per page property
	 *
	 * Posts per page limit to check against.
	 *
	 * @since      0.14.0
	 * @deprecated 1.0.0  Property is used by the WP version of the sniff.
	 *
	 * @var int
	 */
	public $posts_per_page = 100;

	/**
	 * Keep track of whether the deprecated property warning has been thrown.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $thrown = array(
		'FoundDeprecatedProperty' => false,
	);

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
	 *
	 * @param  string $key   Array index / key.
	 * @param  mixed  $val   Assigned value.
	 * @param  int    $line  Token line.
	 * @param  array  $group Group definition.
	 * @return mixed         FALSE if no match, TRUE if matches, STRING if matches
	 *                       with custom error message passed to ->process().
	 */
	public function callback( $key, $val, $line, $group ) {
		if ( 100 !== (int) $this->posts_per_page
			&& false === $this->thrown['FoundDeprecatedProperty']
		) {
			$this->phpcsFile->addWarning(
				'The "posts_per_page" property for the "WordPress.VIP.PostsPerPage" sniff is deprecated. The detection of high pagination limits has been moved to the "WordPress.WP.PostsPerPage" sniff. Please update your custom ruleset.',
				0,
				'FoundDeprecatedProperty'
			);

			$this->thrown['FoundDeprecatedProperty'] = true;
		}

		$key = strtolower( $key );

		if ( ( 'nopaging' === $key && ( 'true' === $val || 1 === $val ) )
			|| ( \in_array( $key, array( 'numberposts', 'posts_per_page' ), true ) && '-1' === $val )
		) {
			return 'Disabling pagination is prohibited in VIP context, do not set `%s` to `%s` ever.';
		}

		return false;
	}

}
