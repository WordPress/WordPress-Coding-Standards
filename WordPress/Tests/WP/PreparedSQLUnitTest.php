<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the PreparedSQL sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.8.0
 */
class WordPress_Tests_WP_PreparedSQLUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @since 0.8.0
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			3 => 1,
			4 => 1,
			5 => 1,
			7 => 1,
			8 => 1,
			16 => 1,
			17 => 1,
			18 => 1,
			20 => 1,
			21 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @since 0.8.0
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();

	}

} // End class.
