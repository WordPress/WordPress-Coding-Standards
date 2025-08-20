<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Security;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the Security_SafeRemoteRequest sniff.
 *
 * @since x.y.z
 *
 * @covers \WordPressCS\WordPress\Sniffs\Security\SafeRemoteRequestSniff
 */
final class SafeRemoteRequestUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList() {
		return array();
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList() {
		return array(
			3  => 1,
			4  => 1,
			5  => 1,
			6  => 1,
			15 => 1,
			16 => 1,
			17 => 1,
			18 => 1,
			27 => 1,
			28 => 1,
			29 => 1,
			30 => 1,
			48 => 1,
			49 => 1,
			50 => 1,
			51 => 1,
			54 => 1,
			55 => 1,
			58 => 1,
			59 => 1,
			75 => 1,
			76 => 1,
		);
	}
}
