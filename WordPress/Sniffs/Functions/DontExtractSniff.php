<?php
/**
 * WordPress Coding Standard.
 *
 * @package  PHP\CodeSniffer\WordPress-Coding-Standards
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Restricts the usage of extract().
 *
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#dont-extract
 *
 * @package  PHP\CodeSniffer\WordPress-Coding-Standards
 * @author   Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_Functions_DontExtractSniff extends WordPress_AbstractFunctionRestrictionsSniff {

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

			'extract' => array(
				'type'      => 'error',
				'message'   => '%s() usage is highly discouraged, due to the complexity and unintended issues it might cause.',
				'functions' => array(
					'extract',
				),
			),

		);
	} // end getGroups()

} // End class.
