<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\Functions;

use WordPress\AbstractFunctionRestrictionsSniff;

/**
 * Restricts usage of some functions.
 *
 * @package    WPCS\WordPressCodingStandards
 *
 * @since      0.3.0
 * @since      0.13.0 Class name changed: this class is now namespaced.
 *
 * @deprecated 0.10.0 The functionality which used to be contained in this class has been moved to
 *                    the WordPress_AbstractFunctionRestrictionsSniff class.
 *                    This class is left here to prevent backward-compatibility breaks for
 *                    custom sniffs extending the old class and references to this
 *                    sniff from custom phpcs.xml files.
 * @see        \WordPress\AbstractFunctionRestrictionsSniff
 */
class FunctionRestrictionsSniff extends AbstractFunctionRestrictionsSniff {

	/**
	 * Groups of functions to restrict.
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
		return array();
	}

}
