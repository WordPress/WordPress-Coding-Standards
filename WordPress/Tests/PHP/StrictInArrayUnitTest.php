<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the StrictInArray sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.9.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class StrictInArrayUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array();
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			5  => 1,
			6  => 1,
			7  => 1,
			23 => 1,
			24 => 1,
			25 => 1,
			26 => 1,
			34 => 1,
			35 => 1,
			36 => 1,
			37 => 1,
		);
	}

}
