<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the PreparedSQL sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.8.0
 */
class WordPress_Tests_WP_PreparedSQLUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @since 0.8.0
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		$errors = array(
			3 => 1,
			4 => 1,
			5 => 1,
			7 => 1,
			8 => 1,
			16 => 1,
			17 => 1,
			18 => 1,
			20 => 1,
			21 => 1,
			54 => 1,
			64 => 1,
			71 => 1,
		);

		// Deal with PHP 5.2 not recognizing quoted heredoc openers, nor nowdoc syntax.
		// These are all false positives!
		if ( PHP_VERSION_ID < 50300 ) {
			$errors[68] = 2;
			$errors[69] = 2;
			$errors[70] = 2;
			$errors[71] = 4;
			$errors[75] = 2;
			$errors[76] = 7;
			$errors[77] = 4;
			$errors[78] = 5;
			$errors[79] = 7;
			$errors[80] = 1;
		}

		return $errors;
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @since 0.8.0
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();

	}

} // End class.
