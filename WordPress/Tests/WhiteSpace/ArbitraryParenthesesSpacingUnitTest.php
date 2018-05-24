<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ArbitraryParenthesesSpacing sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 */
class ArbitraryParenthesesSpacingUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			64  => 4,
			66  => 1,
			68  => 1,
			69  => 1,
			72  => 2,
			73  => 2,
			77  => 2,
			81  => 4,
			90  => 4,
			94  => 1,
			95  => 1,
			97  => 1,
			100 => 2,
			101 => 2,
			104 => 2,
			107 => 2,
			109 => 4,
			111 => 4,
			113 => 2,
			115 => 2,
			123 => 1,
			125 => 2,
			127 => 1,
			131 => 1,
			133 => 1,
			137 => 1,
			139 => 2,
			141 => 1,
			144 => 1,
			146 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			55 => 1,
			56 => 1,
		);
	}

}
