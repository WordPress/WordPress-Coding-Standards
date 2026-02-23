<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\PHP;

use WordPressCS\WordPress\AbstractFunctionRestrictionsSniff;

/**
 * Forbids the use of various native PHP functions and suggests alternatives.
 *
 * @since 0.14.0
 */
final class RestrictedPHPFunctionsSniff extends AbstractFunctionRestrictionsSniff {

	/**
	 * Groups of functions to forbid.
	 *
	 * Example: groups => array(
	 *  'lambda' => array(
	 *      'type'      => 'error' | 'warning',
	 *      'message'   => 'Use anonymous functions instead please!',
	 *      'functions' => array( 'file_get_contents', 'create_function' ),
	 *  )
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			'create_function' => array(
				'type'      => 'error',
				'message'   => '%s() must not be used as it can pose a security risk. Use a declared function or an anonymous function (closure) instead.',
				'functions' => array(
					'create_function',
				),
			),
		);
	}
}
