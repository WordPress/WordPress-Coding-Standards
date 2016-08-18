<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the ClassOpeningStatement sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.10.0
 */
class WordPress_Tests_Classes_ClassOpeningStatementUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {

		return array(
			19 => 2,
			23 => 1,
			28 => 2,
			34 => 1,
			34 => 1,
			38 => 1,
			41 => 1,
			44 => 1,
			47 => 1,
			70 => 1,
			79 => 1,
		);

	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			51 => 1,
		);

	}

} // End class.
