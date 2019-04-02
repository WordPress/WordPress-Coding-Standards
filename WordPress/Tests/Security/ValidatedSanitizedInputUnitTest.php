<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Security;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ValidatedSanitizedInput sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  This sniff has been moved from the `VIP` category to the `Security` category.
 */
class ValidatedSanitizedInputUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			5   => 3,
			7   => 1,
			10  => 1,
			20  => 1,
			33  => 3,
			65  => 1,
			79  => 1,
			80  => 1,
			81  => 1,
			82  => 1,
			85  => 1,
			90  => 1,
			93  => 1,
			96  => 1,
			100 => 2,
			101 => 1,
			104 => 2,
			105 => 1,
			114 => 2,
			121 => 1,
			132 => 1,
			137 => 1,
			138 => 1,
			150 => 2,
			160 => 2,
			164 => 2,
			189 => 1,
			202 => 1,
			206 => 1,
			210 => 1,
			216 => 1,
			217 => 1,
			238 => 1,
			242 => 1,
			245 => 1,
			251 => 1,
			257 => 1,
			266 => 1,
			277 => 1,
			290 => 2,
			300 => 1,
			305 => 2,
			306 => 2,
			307 => 2,
			309 => 2,
			310 => 2,
			311 => 2,
			315 => 2,
			317 => 1,
			323 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			76 => 1, // Whitelist comment deprecation warning.
		);
	}

}
