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
 * Unit test class for the NonceVerification sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.5.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  This sniff has been moved from the `CSRF` category to the `Security` category.
 */
class NonceVerificationUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {

		return array(
			5   => 1,
			9   => 1,
			31  => 1,
			44  => 1,
			48  => 1,
			69  => 1,
			89  => 1,
			113 => 1,
			114 => 1,
			122 => 1,
			126 => 1,
			148 => 1,
			150 => 1,
			159 => 1,
			160 => 1,
			161 => 1,
			177 => 1,
			185 => 1,
			190 => 1,
			198 => 1,
			202 => 1,
			252 => 1,
			269 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			88 => 1, // Whitelist comment deprecation warning.
		);
	}

}
