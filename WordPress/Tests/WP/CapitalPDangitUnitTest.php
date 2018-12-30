<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\WP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the CapitalPDangit sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.12.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class CapitalPDangitUnitTest extends AbstractSniffUnitTest {

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
			3   => 1,
			5   => 1,
			8   => 1,
			26  => 1,
			28  => 1,
			34  => 1,
			35  => 1,
			36  => 1,
			40  => 1,
			41  => 1,
			45  => 1,
			53  => 1,
			60  => 1,
			61  => 1,
			62  => 1,
			65  => 1,
			66  => 1,
			68  => 1,
			101 => 1,
			139 => 1,
			146 => 0, // False negative.
			167 => 1, // Whitelist comment deprecation warning.
			173 => 1,
			181 => 1,
		);
	}

}
