<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\Functions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ReturnType sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 */
class ReturnTypeUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			4 => 1,
			5 => 1,
			6 => 1,
			7 => 1,
			9 => 1,
			11 => 1,
			13 => 1,
			16 => 1,
			36 => 2,
			45 => 1,
			51 => 2,
			59 => 2,
			64 => 2,
			65 => 2,
			66 => 2,
			67 => 3,
			68 => 3,
			69 => 2,
			70 => 2,
			71 => 2,
			72 => 1,
			73 => 2,
			74 => 1,
			75 => 2,
			76 => 1,
			77 => 2,
			78 => 1,
			79 => 2,
			80 => 1,
			81 => 2,
			82 => 1,
			84 => 2,
			85 => 1,
			87 => 2,
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

}
