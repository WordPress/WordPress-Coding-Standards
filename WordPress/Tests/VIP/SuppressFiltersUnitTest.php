<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\VIP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the SuppressFilters sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.14.0
 */
class SuppressFiltersUnitTest extends AbstractSniffUnitTest {

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
			9  => 1,
			10 => 1,
			11 => 1,
			13 => 1,
			14 => 1,
			15 => 1,
			24 => 1,
			25 => 1,
			26 => 1,
			28 => 1,
			29 => 1,
			30 => 1,
			35 => 1,
			36 => 1,
			37 => 1,
			41 => 1,
			42 => 1,
			43 => 1,
			46 => 1,
			47 => 1,
			48 => 1
		);

	}

} // End class.
