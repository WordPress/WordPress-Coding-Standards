<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the CronInterval sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.3.0
 */
class WordPress_Tests_VIP_CronIntervalUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			12 => 1,
			17 => 1,
			37 => 1,
			43 => 1,
			53 => 1,
			56 => 1, // False positive.
			67 => 1,
		);

	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			39 => 1,
			41 => 1,
			76 => 1,
		);

	}

} // End class.
