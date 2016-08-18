<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the EscapeOutput sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   2013-06-11
 */
class WordPress_Tests_XSS_EscapeOutputUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			17 => 1,
			19 => 1,
			36 => 1,
			39 => 1,
			40 => 1,
			41 => 1,
			43 => 1,
			46 => 1,
			53 => 1,
			59 => 1,
			60 => 1,
			65 => 1,
			68 => 1,
			71 => 1,
			73 => 1,
			75 => 1,
			101 => 1,
			103 => 1,
			111 => 1,
			112 => 1,
			113 => 1,
			114 => 1,
			125 => 1,
			131 => 1,
			135 => 1,
			138 => 1,
			145 => 1,
			147 => 1,
			149 => 1,
			152 => 2,
			159 => 1,
			162 => 1,
			169 => 1,
			172 => 1,
			173 => 1,
			182 => 3,
		);

	} // end getErrorList()

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();

	}

} // End class.
