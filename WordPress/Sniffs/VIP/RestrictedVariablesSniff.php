<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\VIP;

use WordPress\AbstractVariableRestrictionsSniff;

/**
 * Restricts usage of some variables in VIP context.
 *
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class RestrictedVariablesSniff extends AbstractVariableRestrictionsSniff {

	/**
	 * Groups of variables to restrict.
	 *
	 * Example: groups => array(
	 *  'wpdb' => array(
	 *      'type'          => 'error' | 'warning',
	 *      'message'       => 'Dont use this one please!',
	 *      'variables'     => array( '$val', '$var' ),
	 *      'object_vars'   => array( '$foo->bar', .. ),
	 *      'array_members' => array( '$foo['bar']', .. ),
	 *  )
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#working-with-wp_users-and-user_meta
			'user_meta' => array(
				'type'        => 'error',
				'message'     => 'Usage of users/usermeta tables is highly discouraged in VIP context, For storing user additional user metadata, you should look at User Attributes.',
				'object_vars' => array(
					'$wpdb->users',
					'$wpdb->usermeta',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#caching-constraints
			'cache_constraints' => array(
				'type'          => 'warning',
				'message'       => 'Due to using Batcache, server side based client related logic will not work, use JS instead.',
				'variables'     => array(
					'$_COOKIE',
				),
				'array_members' => array(
					'$_SERVER[\'HTTP_USER_AGENT\']',
					'$_SERVER[\'REMOTE_ADDR\']',
				),
			),
		);
	}

} // End class.
