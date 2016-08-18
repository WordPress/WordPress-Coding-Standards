<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the ValidFunctionName sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   2013-06-11
 */
class WordPress_Tests_NamingConventions_ValidFunctionNameUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			3 => 1,
			9 => 1,
			13 => 1,
			15 => 1,
			79 => 1,
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
		);

	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();

	}

} // End class.
