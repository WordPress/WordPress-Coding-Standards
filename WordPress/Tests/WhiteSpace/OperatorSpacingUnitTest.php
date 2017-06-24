<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the OperatorSpacing sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   2013-06-11
 * @since   0.12.0     Now only tests the WPCS specific addition of T_BOOLEAN_NOT.
 *                     The rest of the sniff is unit tested upstream.
 */
class WordPress_Tests_WhiteSpace_OperatorSpacingUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			23 => 2,
			28 => 2,
			40 => 2,
			41 => 2,
			42 => 2,
			43 => 2,
			44 => 2,
			47 => 2,
			48 => 2,
			49 => 2,
			50 => 2,
			51 => 2,
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
