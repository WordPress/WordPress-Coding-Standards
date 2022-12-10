<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\DB;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the DirectDatabaseQuery sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  This sniff has been moved from the `VIP` category to the `DB` category.
 */
final class DirectDatabaseQueryUnitTest extends AbstractSniffUnitTest {

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
			5   => 2,
			12  => 1,
			26  => 2,
			27  => 2,
			28  => 1,
			29  => 1,
			38  => 2,
			44  => 2,
			60  => 1,
			61  => 1,
			62  => 1,
			63  => 1,
			65  => 2,
			66  => 2,
			67  => 2,
			80  => 1,
			81  => 1,
			82  => 1,
			83  => 1,
			84  => 1,
			85  => 1,
			86  => 1,
			97  => 1,
			114 => 1,
			123 => 1,
			130 => 1,
			141 => 1,
			150 => 2,
			157 => 2,
			168 => 2,
			175 => 1,
			180 => 1,
			185 => 1,
			190 => 1,
			195 => 1,
			200 => 1,
			205 => 1,
			210 => 1,
			215 => 1,
			220 => 1,
			228 => 2,
			235 => 2,
			251 => 1,
			252 => 1,
			265 => 1,
			269 => 1,
			281 => 1,
			287 => 2,
			288 => 1,
			300 => 1,
			306 => 2,
		);
	}
}
