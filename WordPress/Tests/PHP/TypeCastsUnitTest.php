<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the TypeCasts sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since 1.2.0
 */
class TypeCastsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			12 => 1,
			13 => 1,
			14 => 1,
			15 => 1,
			24 => 1,
			25 => 2,
			26 => 1,
			27 => 2,
			28 => 1,
			29 => 2,
			30 => 2,
			31 => 1,
			32 => 1,
			33 => 1,
			34 => 1,
			35 => 1,
			38 => 1,
			39 => 2,
			40 => 1,
			41 => 2,
			42 => 1,
			43 => 2,
			44 => 2,
			45 => 1,
			46 => 1,
			47 => 1,
			48 => 1,
			49 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			18 => 1,
			19 => 1,
			// 20 => 1,
			21 => 1,
			34 => 1,
			35 => 1,
			48 => 1,
			49 => 1,
		);
	}
}
