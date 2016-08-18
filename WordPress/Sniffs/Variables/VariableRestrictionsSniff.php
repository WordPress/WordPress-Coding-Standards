<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Restricts usage of some variables.
 *
 * @package    WPCS\WordPressCodingStandards
 *
 * @since      0.3.0
 * @deprecated 0.10.0 The functionality which used to be contained in this class has been moved to
 *                    the WordPress_AbstractVariableRestrictionsSniff class.
 *                    This class is left here to prevent backward-compatibility breaks for
 *                    custom sniffs extending the old class and references to this
 *                    sniff from custom phpcs.xml files.
 *                    This file is also still used to unit test the abstract class.
 * @see        WordPress_AbstractVariableRestrictionsSniff
 */
class WordPress_Sniffs_Variables_VariableRestrictionsSniff extends WordPress_AbstractVariableRestrictionsSniff {

	/**
	 * Groups of variables to restrict.
	 *
	 * Example: groups => array(
	 * 	'wpdb' => array(
	 * 		'type' => 'error' | 'warning',
	 * 		'message' => 'Dont use this one please!',
	 * 		'variables' => array( '$val', '$var' ),
	 * 		'object_vars' => array( '$foo->bar', .. ),
	 * 		'array_members' => array( '$foo['bar']', .. ),
	 * 	)
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return parent::$groups;
	}

} // End class.
