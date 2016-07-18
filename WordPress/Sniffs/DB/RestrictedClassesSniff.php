<?php
/**
 * WordPress Coding Standard.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Verifies that no database related PHP classes are used.
 *
 * "Avoid touching the database directly. If there is a defined function that can get
 *  the data you need, use it. Database abstraction (using functions instead of queries)
 *  helps keep your code forward-compatible and, in cases where results are cached in memory,
 *  it can be many times faster."
 *
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#database-queries
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 */
class WordPress_Sniffs_DB_RestrictedClassesSniff extends WordPress_AbstractClassRestrictionsSniff {

	/**
	 * Groups of classes to restrict.
	 *
	 * Example: groups => array(
	 * 	'lambda' => array(
	 * 		'type'      => 'error' | 'warning',
	 * 		'message'   => 'Avoid direct calls to the database.',
	 * 		'classes'   => array( 'PDO', '\Namespace\Classname' ),
	 * 	)
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(

			'mysql' => array(
				'type'      => 'error',
				'message'   => 'Accessing the database directly should be avoided. Please use the $wpdb object and associated functions instead. Found: %s.',
				'classes' => array(
					'mysqli',
					'PDO',
					'PDOStatement',
				),
			),

		);
	}

} // end class
