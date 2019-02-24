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
 * Unit test class for the IniSet sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since 2.1.0
 */
class IniSetUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			16 => 1,
			17 => 1,
			18 => 1,
			19 => 1,
			20 => 1,
			21 => 1,
			22 => 1,
			23 => 1,
			24 => 1,
			25 => 1,
			26 => 1,
			27 => 1,
			28 => 1,
			29 => 1,
			30 => 1,
			31 => 1,
			32 => 1,
			33 => 1,
			34 => 1,
			42 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			36 => 1,
			37 => 1,
			38 => 1,
			39 => 1,
			43 => 1,
		);
	}

}
