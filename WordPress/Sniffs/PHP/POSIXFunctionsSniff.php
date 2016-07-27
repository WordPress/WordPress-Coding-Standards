<?php
/**
 * WordPress Coding Standard.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Perl compatible regular expressions (PCRE, preg_ functions) should be used in preference
 * to their POSIX counterparts.
 *
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#regular-expressions
 * @link     http://php.net/manual/en/ref.regex.php
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 */
class WordPress_Sniffs_PHP_POSIXFunctionsSniff extends WordPress_AbstractFunctionRestrictionsSniff {

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
			'ereg' => array(
				'type'      => 'error',
				'message'   => '%s has been deprecated since PHP 5.3 and removed in PHP 7.0, please use preg_match() instead.',
				'functions' => array(
					'ereg',
					'eregi',
					'sql_regcase',
				),
			),

			'ereg_replace' => array(
				'type'      => 'error',
				'message'   => '%s has been deprecated since PHP 5.3 and removed in PHP 7.0, please use preg_replace() instead.',
				'functions' => array(
					'ereg_replace',
					'eregi_replace',
				),
			),

			'split' => array(
				'type'      => 'error',
				'message'   => '%s  has been deprecated since PHP 5.3 and removed in PHP 7.0, please use explode(), str_split() or preg_split() instead.',
				'functions' => array(
					'split',
					'spliti',
				),
			),

		);
	} // end getGroups()

} // end class
