<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the VIP_RestrictedFunctions sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.3.0
 */
class WordPress_Tests_VIP_RestrictedFunctionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			3  => 1,
			17 => 1,
			30 => ( PHP_VERSION_ID >= 50300 ) ? 0 : 1,
			32 => 1,
			34 => 1,
			36 => 1,
			38 => 1,
			40 => 1,
			42 => 1,
			44 => 1,
			46 => 1,
			49 => 1,
			50 => 1,
			51 => 1,
			52 => 1,
			53 => 1,
			58 => 1,
			59 => 1,
			60 => 1,
			61 => 1,
			62 => 1,
			63 => 1,
			64 => 1,
			65 => 1,
			66 => 1,
			67 => 1,
			72 => 1,
			73 => 1,
			74 => 1,
			77 => 1,
		);

	} // End getErrorList().

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			5  => 1,
			7  => 1,
			9 => 1,
			11 => 1,
			13 => 1,
			54 => 1,
			55 => 1,
			57 => 1,
			69 => 1,
			76 => 1,
		);

	}

} // End class.
