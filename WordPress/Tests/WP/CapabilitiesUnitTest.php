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
 * Unit test class for the Capabilities sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   1.0.0
 */
class CapabilitiesUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			2  => 1,
			3  => 1,
			4  => 1,
			5  => 1,
			6  => 1,
			7  => 1,
			8  => 1,
			9  => 1,
			10 => 1,
			11 => 1,
			12 => 1,
			16 => 1,
			23 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			30 => 1,
			31 => 1,
			32 => 1,
			33 => 1,
			34 => 1,
			35 => 1,
			36 => 1,
			41 => 1,
			45 => 1,
			53 => 1,
			54 => 1,
		);
	}

}
