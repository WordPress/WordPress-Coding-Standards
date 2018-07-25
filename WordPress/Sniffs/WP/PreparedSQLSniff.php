<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\WP;

use WordPress\Sniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Sniff for prepared SQL.
 *
 * Makes sure that variables aren't directly interpolated into SQL statements.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#formatting-sql-statements
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.8.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * @deprecated 1.0.0  This sniff has been moved to the `DB` category.
 *                    This file remains for now to prevent BC breaks.
 */
class PreparedSQLSniff extends \WordPress\Sniffs\DB\PreparedSQLSniff {

	/**
	 * Keep track of whether the warning has been thrown to prevent
	 * the message being thrown for every token triggering the sniff.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $thrown = array(
		'DeprecatedSniff' => false,
	);

	/**
	 * Don't use.
	 *
	 * @deprecated 1.0.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void|int
	 */
	public function process_token( $stackPtr ) {
		if ( false === $this->thrown['DeprecatedSniff'] ) {
			$this->phpcsFile->addWarning(
				'The "WordPress.WP.PreparedSQL" sniff has been renamed to "WordPress.DB.PreparedSQL". Please update your custom ruleset.',
				0,
				'DeprecatedSniff'
			);

			$this->thrown['DeprecatedSniff'] = true;
		}

		return parent::process_token( $stackPtr );
	}

}
