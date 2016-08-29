<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the StrictInArray sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.9.0
 */
class WordPress_Tests_PHP_StrictInArrayUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			7 => 1,
			20 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			5 => 1,
			9 => 1,
			10 => 1,
			26 => 1,
			27 => 1,
			28 => 1,
			29 => 1,
			37 => 1,
			38 => 1,
			39 => 1,
			40 => 1,
		);
	}

} // End class.
