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
 * Encourages the use of more performant PHP.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since 3.0.0
 */
class PerformantPHPFunctionsSniff extends AbstractFunctionRestrictionsSniff {

	/**
	 * Groups of functions to discourage.
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
			'typecasting' => array(
				'type'      => 'warning',
				'message'   => '%s() should only be used in callbacks. All other cases, using type casting will be more performant.',
				'functions' => array(
					'intval',
					'strval',
					'floatval',
					'boolval',
					'doubleval',
					'setttype',
				),
			),
		);
	}

}
