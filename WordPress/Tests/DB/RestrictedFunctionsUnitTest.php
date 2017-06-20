<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the DB_RestrictedFunctions sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.10.0
 */
class WordPress_Tests_DB_RestrictedFunctionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			25 => 1,
			26 => 1,
			27 => 1,
			28 => 1,
			29 => 1,
			30 => 1,
			31 => 1,
			32 => 1,
			33 => 1,

			36 => 1,
			37 => 1,
			38 => 1,
			39 => 1,
			40 => 1,
			41 => 1,
			42 => 1,
			43 => 1,
			44 => 1,

			47 => 1,
			48 => 1,
			49 => 1,
			50 => 1,
			51 => 1,

			54 => 1,
			55 => 1,
			56 => 1,
			57 => 1,

			60 => 1,

			63 => 1,

			66 => 1,
			67 => 1,
			68 => 1,
			69 => 1,
			70 => 1,
			71 => 1,
			72 => 1,
			73 => 1,
			74 => 1,
			75 => 1,
			76 => 1,
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
