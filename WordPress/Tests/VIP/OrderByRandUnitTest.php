<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package  PHP\CodeSniffer\WordPress-Coding-Standards
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Unit test class for the OrderByRand sniff.
 *
 * @package PHP\CodeSniffer\WordPress-Coding-Standards
 * @author  Weston Ruter <weston@x-team.com>
 * @since   0.9.0
 */
class WordPress_Tests_VIP_OrderByRandUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			4  => 1,
			5  => 1,
			6  => 1,
			9  => 1,
			11 => 1,
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
