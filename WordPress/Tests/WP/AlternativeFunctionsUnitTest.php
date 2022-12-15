<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\WP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the WP_AlternativeFunctions sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class AlternativeFunctionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array();
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			3  => 1,
			4  => 1,
			5  => 1,
			8  => 1,
			10 => 1,
			12 => 1,
			14 => 1,
			15 => 1,
			16 => 1,
			17 => 1,
			18 => 1,
			19 => 1,
			20 => 1,
			21 => 1,
			22 => 1,
			23 => 1,
			24 => 1,
			25 => 1,
			26 => 1,
			38 => 1,
			42 => 1,
			44 => 1,
			45 => 1,
			47 => 1,
			65 => 1,
			66 => 1,
			71 => 1,
			78 => 1,
			80 => 1,
			81 => 1,
			82 => 1,
			83 => 1,
			84 => 1,
			85 => 1,
			86 => 1,
			87 => 1,
			88 => 1,
			89 => 1,
			90 => 1,
		);
	}
}
