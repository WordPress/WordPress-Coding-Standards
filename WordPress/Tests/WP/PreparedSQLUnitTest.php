<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\WP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the PreparedSQL sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.8.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  The sniff has been deprecated. This unit test file now
 *                 only tests that the deprecation warnings are correctly thrown.
 */
class PreparedSQLUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @since 0.8.0
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array();
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @since 0.8.0
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			1 => 1,
		);

	}

} // End class.
