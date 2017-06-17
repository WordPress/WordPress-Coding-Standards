<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the EmptyStatement sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.12.0
 */
class WordPress_Tests_CodeAnalysis_EmptyStatementUnitTest extends AbstractSniffUnitTest {

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
			12 => 1,
			15 => 1,
			18 => 1,
			21 => 1,
			22 => 1,
			31 => 1,
			33 => 1,
			43 => 1,
			45 => 1,
		);
	}

} // End class.
