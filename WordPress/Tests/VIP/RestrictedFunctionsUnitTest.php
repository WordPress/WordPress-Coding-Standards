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
			5  => 1,
			21 => 1,
			34 => version_compare( PHP_VERSION, '5.3.0', '>=' ) ? 0 : 1,
			36 => 1,
			38 => 1,
			40 => 1,
			42 => 1,
			44 => 1,
			46 => 1,
			48 => 1,
			50 => 1,
			53 => 1,
			54 => 1,
			55 => 1,
			56 => 1,
			57 => 1,
			62 => 1,
			63 => 1,
			64 => 1,
			65 => 1,
			66 => 1,
			67 => 1,
			68 => 1,
			69 => 1,
			70 => 1,
			71 => 1,
			74 => 1,
			75 => 2,
			76 => 1,
			77 => 1,
			78 => 1,
			79 => 1,
			80 => 1,
			81 => 1,
			82 => 1,
			83 => 1,
			84 => 1,
			85 => 1,
			87 => 1,
			92 => 1,
			93 => 1,
			94 => 1,
			97 => 1,
			98 => 1,
			99 => 1,
			100 => 1,
			101 => 1,
			103 => 1,
		);

	} // end getErrorList()

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			7  => 1,
			9  => 1,
			11 => 1,
			13 => 1,
			15 => 1,
			17 => 1,
			58 => 1,
			59 => 1,
			61 => 1,
			72 => 1,
			88 => 1,
			95 => 1,
			96 => 1,
			102 => 1,
			104 => 1,
		);

	}

} // End class.
