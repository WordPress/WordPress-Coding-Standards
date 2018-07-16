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
 * Flag using orderby => rand.
 *
 * @link    https://vip.wordpress.com/documentation/code-review-what-we-look-for/#order-by-rand
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.9.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * @deprecated 1.0.0  This sniff has been deprecated.
 *                    This file remains for now to prevent BC breaks.
 */
class OrderByRandSniff extends AbstractArrayAssignmentRestrictionsSniff {

	/**
	 * Keep track of whether the warnings have been thrown to prevent
	 * the messages being thrown for every token triggering the sniff.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $thrown = array(
		'DeprecatedSniff'                 => false,
		'FoundPropertyForDeprecatedSniff' => false,
	);

	/**
	 * Groups of variables to restrict.
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			'orderby' => array(
				'type' => 'error',
				'keys' => array(
					'orderby',
				),
			),
		);
	}

	/**
	 * Process the token and handle the deprecation notices.
	 *
	 * @since 1.0.0 Added to allow for throwing the deprecation notices.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void|int
	 */
	public function process_token( $stackPtr ) {
		if ( false === $this->thrown['DeprecatedSniff'] ) {
			$this->thrown['DeprecatedSniff'] = $this->phpcsFile->addWarning(
				'The "WordPress.VIP.OrderByRand" sniff has been deprecated. Please update your custom ruleset.',
				0,
				'DeprecatedSniff'
			);
		}

		if ( ! empty( $this->exclude )
			&& false === $this->thrown['FoundPropertyForDeprecatedSniff']
		) {
			$this->thrown['FoundPropertyForDeprecatedSniff'] = $this->phpcsFile->addWarning(
				'The "WordPress.VIP.OrderByRand" sniff has been deprecated. Please update your custom ruleset.',
				0,
				'FoundPropertyForDeprecatedSniff'
			);
		}

		return parent::process_token( $stackPtr );
	}

	/**
	 * Callback to process each confirmed key, to check value
	 * This must be extended to add the logic to check assignment value
	 *
	 * @param  string $key   Array index / key.
	 * @param  mixed  $val   Assigned value.
	 * @param  int    $line  Token line.
	 * @param  array  $group Group definition.
	 * @return mixed         FALSE if no match, TRUE if matches, STRING if matches with custom error message passed to ->process().
	 */
	public function callback( $key, $val, $line, $group ) {
		if ( 'rand' === strtolower( $val ) ) {
			return 'Detected forbidden query_var "%s" of "%s". Use vip_get_random_posts() instead.';
		} else {
			return false;
		}
	}

}
