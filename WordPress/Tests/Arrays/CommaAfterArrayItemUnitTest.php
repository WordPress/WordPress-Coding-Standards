<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the Arrays.CommaAfterArrayItem sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.12.0
 */
class WordPress_Tests_Arrays_CommaAfterArrayItemUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			44  => 2,
			45  => 2,
			48  => 2,
			49  => 2,
			52  => 1,
			53  => 1,
			57  => 1,
			58  => 1,
			59  => 1,
			62  => 1,
			66  => 1,
			67  => 1,
			72  => 1,
			75  => 1,
			82  => 1,
			87  => 1,
			90  => 4,
			91  => 4,
			93  => 2,
			94  => 1,
			98  => 1,
			100 => 2,
			102 => 1,
			104 => 1,
			106 => 1,
			112 => 1,
			113 => 2,
			117 => 1,
			118 => 2,
			122 => 2,
			123 => 2,
			128 => 1,
			135 => 1,
			140 => 1,
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
