<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\VIP;

use WordPress\AbstractArrayAssignmentRestrictionsSniff;

/**
 * Flag potentially slow queries.
 *
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#uncached-pageload
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.12.0 Introduced new and more intuitively named 'slow query' whitelist
 *                 comment, replacing the 'tax_query' whitelist comment which is now
 *                 deprecated.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class SlowDBQuerySniff extends AbstractArrayAssignmentRestrictionsSniff {

	/**
	 * Groups of variables to restrict.
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
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {

		if ( $this->has_whitelist_comment( 'slow query', $stackPtr ) ) {
			return;
		}

		if ( $this->has_whitelist_comment( 'tax_query', $stackPtr ) ) {
			/*
			 * Only throw the warning about a deprecated comment when the sniff would otherwise
			 * have been triggered on the array key.
			 */
			if ( in_array( $this->tokens[ $stackPtr ]['code'], array( T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING ), true ) ) {
				$this->phpcsFile->addWarning(
					'The "tax_query" whitelist comment is deprecated in favor of the "slow query" whitelist comment.',
					$stackPtr,
					'DeprecatedWhitelistFlagFound'
				);
			}

			return;
		}

		return parent::process_token( $stackPtr );
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
