<?php
/**
 * WordPress Coding Standard.
 *
 * @package PHP\CodeSniffer\WordPress-Coding-Standards
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Restricts usage of some variables.
 *
 * @package    PHP\CodeSniffer\WordPress-Coding-Standards
 * @author     Shady Sharaf <shady@x-team.com>
 *
 * @since      2014-12-11
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
