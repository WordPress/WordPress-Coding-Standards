<?php
/**
 * Flag slow queries
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_VIP_SlowDBQuerySniff extends WordPress_Sniffs_Arrays_ArrayAssignmentRestrictionsSniff
{

	/**
	 * Groups of variables to restrict
	 * This should be overridden in extending classes.
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
		return array(
			'slow_db_query' => array(
				'type' => 'warning',
				'message' => 'Detected usage of %s, possible slow query.',
				'keys' => array(
					'tax_query',
					'meta_query',
					'meta_key',
					'meta_value',
					),
				)
			);
	}
}//end class
