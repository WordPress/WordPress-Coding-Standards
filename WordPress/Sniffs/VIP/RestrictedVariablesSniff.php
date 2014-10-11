<?php
/**
 * Restricts usage of some variables in VIP context
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_VIP_RestrictedVariablesSniff extends WordPress_Sniffs_Variables_VariableRestrictionsSniff
{

	/**
	 * Groups of variables to restrict
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
			'user_meta' => array(
				'type' => 'error',
				'message' => 'Usage of users/usermeta tables is highly discouraged in VIP context, For storing user additional user metadata, you should look at User Attributes.',
				'object_vars' => array(
					'$wpdb->users',
					'$wpdb->usermeta',
					),
				),
			'cache_constraints' => array(
				'type' => 'warning',
				'message' => 'Due to using Batcache, server side based client related logic will not work, use JS instead.',
				'variables' => array(
					'$_COOKIE',
					),
				'array_members' => array(
					'$_SERVER[\'HTTP_USER_AGENT\']',
					'$_SERVER[\'REMOTE_ADDR\']',
					),
				),
			);
	}


}//end class
