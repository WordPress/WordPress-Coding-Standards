<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Flag potentially slow queries.
 *
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#uncached-pageload
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 */
class WordPress_Sniffs_VIP_SlowDBQuerySniff extends WordPress_AbstractArrayAssignmentRestrictionsSniff {

	/**
	 * Groups of variables to restrict.
	 * This should be overridden in extending classes.
	 *
	 * Example: groups => array(
	 * 	'wpdb' => array(
	 * 		'type'          => 'error' | 'warning',
	 * 		'message'       => 'Dont use this one please!',
	 * 		'variables'     => array( '$val', '$var' ),
	 * 		'object_vars'   => array( '$foo->bar', .. ),
	 * 		'array_members' => array( '$foo['bar']', .. ),
	 * 	)
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			'slow_db_query' => array(
				'type'    => 'warning',
				'message' => 'Detected usage of %s, possible slow query.',
				'keys'    => array(
					'tax_query',
					'meta_query',
					'meta_key',
					'meta_value',
				),
			),
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 0.10.0
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

		$this->init( $phpcsFile );

		if ( $this->has_whitelist_comment( 'tax_query', $stackPtr ) ) {
			return;
		}

		parent::process( $phpcsFile, $stackPtr );
	}

	/**
	 * Callback to process each confirmed key, to check value.
	 * This must be extended to add the logic to check assignment value.
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

} // End class.
