<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\Arrays;

use WordPress\AbstractArrayAssignmentRestrictionsSniff;

/**
 * Restricts array assignment of certain keys.
 *
 * @package    WPCS\WordPressCodingStandards
 *
 * @since      0.3.0
 * @since      0.13.0 Class name changed: this class is now namespaced.
 *
 * @deprecated 0.10.0 The functionality which used to be contained in this class has been moved to
 *                    the WordPress_AbstractArrayAssignmentRestrictionsSniff class.
 *                    This class is left here to prevent backward-compatibility breaks for
 *                    custom sniffs extending the old class and references to this
 *                    sniff from custom phpcs.xml files.
 *                    This file is also still used to unit test the abstract class.
 * @see        \WordPress\AbstractArrayAssignmentRestrictionsSniff
 */
class ArrayAssignmentRestrictionsSniff extends AbstractArrayAssignmentRestrictionsSniff {

	/**
	 * Groups of variables to restrict.
	 *
	 * Example: groups => array(
	 *  'groupname' => array(
	 *      'type'     => 'error' | 'warning',
	 *      'message'  => 'Dont use this one please!',
	 *      'keys'     => array( 'key1', 'another_key' ),
	 *      'callback' => array( 'class', 'method' ), // Optional.
	 *  )
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array();
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
		return true;
	}

}
