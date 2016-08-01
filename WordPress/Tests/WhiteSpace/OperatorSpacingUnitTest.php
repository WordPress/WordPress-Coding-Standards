<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package PHP\CodeSniffer\WordPress-Coding-Standards
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the OperatorSpacing sniff.
 *
 * @package PHP\CodeSniffer\WordPress-Coding-Standards
 * @author  Akeda Bagus <akeda@x-team.com>
 * @since   2013-06-11
 */
class WordPress_Tests_WhiteSpace_OperatorSpacingUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			5  => 4,
			18 => 1,
			45 => 1,
			49 => 1,
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
