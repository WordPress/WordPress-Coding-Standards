<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package  PHP\CodeSniffer\WordPress-Coding-Standards
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Unit test class for the PreparedSQL sniff.
 *
 * @package PHP\CodeSniffer\WordPress-Coding-Standards
 * @author  J.D. Grimes <jdg@codesymphony.co>
 * @since   0.8.0
 */
class WordPress_Tests_WP_PreparedSQLUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
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
