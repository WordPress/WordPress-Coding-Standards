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
 * Unit test class for the PHP.NoSilencedErrors sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   1.1.0
 */
class NoSilencedErrorsUnitTest extends AbstractSniffUnitTest {

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
			7  => 1,
			8  => 1,
			9  => 1,
			10 => 1,
			11 => 1,
			20 => 1,
			21 => 1,
			26 => 1,
			29 => 1,
			35 => 1,
			37 => 1,
			40 => 1,
			44 => 1,
			48 => 1,
			58 => 2,
			59 => 1,
			63 => 1,
			64 => 1,
			65 => 1,
			66 => 1,
			68 => 1,
			71 => 1,
			78 => 1,
		);
	}

}
