<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the TypeCasts sniff.
 *
 * @since 1.2.0
 *
 * @covers \WordPressCS\WordPress\Sniffs\PHP\TypeCastsSniff
 */
final class TypeCastsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList() {
		return array(
			10 => 1,
			11 => 1,
			13 => 1,
			26 => 1,
			27 => 1,
			28 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList() {
		return array(
			15 => 1,
			16 => 1,
			17 => 1,
			29 => 1,
		);
	}
}
