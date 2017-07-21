<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the ArrayDeclarationSpacing sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.11.0
 */
class WordPress_Tests_Arrays_ArrayDeclarationSpacingUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			5  => 2,
			8  => 2,
			10 => 1,
			13 => 4,
			17 => 2,
			19 => 1,
			23 => 1,
			26 => 1,
			29 => 1,
			35 => 1,
			36 => 1,
			42 => 1,
			46 => 1,
			59 => 2,
			60 => 1,
			61 => 1,
			81 => 1,
			82 => 1,
			85 => 1,
			86 => 2,
			89 => 3,
			91 => 1,
			94 => 1,
			95 => 2,
			98 => 1,
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
