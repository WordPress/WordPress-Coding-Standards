<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package  PHP\CodeSniffer\WordPress-Coding-Standards
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Unit test class for the SessionVariableUsage sniff.
 *
 * @package PHP\CodeSniffer\WordPress-Coding-Standards
 * @author  Shady Sharaf <shady@x-team.com>
 * @since   2014-12-11
 */
class WordPress_Tests_VIP_SessionVariableUsageUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			3 => 1,
			4 => 1,
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
