<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Restricts the use of various deprecated WordPress functions and suggests alternatives.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0
 */
class WordPress_Sniffs_WP_DeprecatedFunctionsSniff extends WordPress_AbstractFunctionRestrictionsSniff {

	/**
	 * Groups of functions to restrict.
	 *
	 * Example: groups => array(
	 * 	'lambda' => array(
	 * 		'type'      => 'error' | 'warning',
	 * 		'message'   => 'Use anonymous functions instead please!',
	 * 		'functions' => array( 'eval', 'create_function' ),
	 * 	)
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			'get_postdata' => array(
				'type'      => 'error',
				'message'   => '%s has been deprecated since WordPress 1.5.1. Use get_post() instead.',
				'functions' => array(
					'get_postdata',
				),
			),

			'start_wp' => array(
				'type'      => 'error',
				'message'   => '%s has been deprecated since WordPress 1.5 Use the Loop instead.',
				'functions' => array(
					'get_postdata',
				),
			),

		);
	} // end getGroups()

} // end class
