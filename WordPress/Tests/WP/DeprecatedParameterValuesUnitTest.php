<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\WP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the DeprecatedParameterValues sniff.
 *
 * @since 1.0.0
 *
 * @covers \WordPressCS\WordPress\Sniffs\WP\DeprecatedParameterValuesSniff
 */
final class DeprecatedParameterValuesUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList() {
		return array(
			5  => 1,
			6  => 1,
			7  => 1,
			8  => 1,
			9  => 1,
			10 => 1,
			11 => 1,
			12 => 1,
			13 => 1,
			14 => 1,
			15 => 1,
			16 => 1,
			17 => 1,
			18 => 1,
			35 => 1,
			40 => 1,
			43 => 1,
			44 => 1,
			45 => 1,
			46 => 1,
			47 => 1,
			48 => 1,
			49 => 1,
			50 => 1,
			51 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList() {
		return array(
			55 => 1,
			56 => 1,
		);
	}
}
