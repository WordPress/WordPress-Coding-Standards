<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the SuppressFilters sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.14.0
 */
class WordPress_Tests_VIP_SuppressFiltersTest extends AbstractSniffUnitTest {

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
			7  => 1,
			8  => 1,
			9  => 1,
			15 => 1,
			16 => 1,
			17 => 1,
			25 => 1,
			26 => 1,
			27 => 1,
			30 => 1,
			31 => 1,
			32 => 1,
			35 => 1,
			36 => 1,
			37 => 1,
			42 => 1,
			43 => 1,
			44 => 1,
			46 => 1,
			47 => 1,
			48 => 1,
			50 => 1,
			51 => 1,
			52 => 1,
		);

	}

} // End class.
