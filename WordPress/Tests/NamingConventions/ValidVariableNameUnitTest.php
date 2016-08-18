<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the ValidVariableName sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.9.0
 */
class WordPress_Tests_NamingConventions_ValidVariableNameUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		$errors = array(
			2   => 1,
			5   => 1,
			8   => 1,
			11   => 1,
			13   => 1,
			16   => 1,
			18   => 1,
			21   => 1,
			23   => 1,
			26   => 1,
			29   => 1,
			32   => 1,
			34   => 1,
			38   => 1,
			40   => 1,
			43   => 1,
			51   => 1,
			54   => 1,
			55   => 1,
			57   => 1,
			60   => 1,
			61   => 1,
			64   => 1,
			65   => 1,
			70   => 1,
			77   => 1,
			78   => 1,
			79   => 1,
			80   => 1,
			82   => 1,
			86   => 1,
			87   => 1,
			88   => 1,
			99   => 1,
			100  => 1,
			101  => 1,
			102  => 1,
			103  => 1,
			104  => 1,
			105  => 1,
			121  => 1,
		);

		return $errors;

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
