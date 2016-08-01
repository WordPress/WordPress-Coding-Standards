<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package  PHP\CodeSniffer\WordPress-Coding-Standards
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Unit test class for the VIP_RestrictedVariables sniff.
 *
 * @package PHP\CodeSniffer\WordPress-Coding-Standards
 * @author  Shady Sharaf <shady@x-team.com>
 * @since   2014-12-11
 */
class WordPress_Tests_VIP_RestrictedVariablesUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			3 => 1,
			5 => 1,
			7 => 1,
			9 => 1,
		);

	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			13 => 1,
			14 => 1,
			17 => 1,
		);

	}

} // End class.
