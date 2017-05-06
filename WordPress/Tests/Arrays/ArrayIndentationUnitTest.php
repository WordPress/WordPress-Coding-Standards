<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the ArrayIndentation sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.12.0
 */
class WordPress_Tests_Arrays_ArrayIndentationUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			23 => 1,
			24 => 1,
			25 => 1,
			28 => 1,
			29 => 1,
			30 => 1,
			33 => 1,
			34 => 1,
			36 => 1,
			38 => 1,
			39 => 1,
			40 => 1,
			44 => 1,
			45 => 1,
			46 => 1,
			50 => 1,
			51 => 1,
			52 => 1,
			55 => 1,
			57 => 1,
			58 => 1,
			60 => 1,
			61 => 1,
			66 => 1,
			80 => 1,
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
