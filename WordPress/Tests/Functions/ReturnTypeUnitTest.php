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
			8 => 1,
			9 => 1,
			10 => 1,
			29 => 2,
			38 => 1,
			44 => 2,
			52 => 2,
			57 => 2,
			58 => 2,
			59 => 2,
			60 => 3,
			61 => 3,
			62 => 2,
			63 => 1,
			64 => 2,
			65 => 1,
			66 => 2,
			67 => 1,
			68 => 2,
			69 => 1,
			70 => 2,
			71 => 1,
			72 => 2,
			73 => 1,
			74 => 2,
			75 => 1,
			76 => 2,
			77 => 1,
			79 => 2,
			80 => 1,
			82 => 1,
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
