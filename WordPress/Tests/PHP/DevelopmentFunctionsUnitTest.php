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
 * Unit test class for the PHP_DevelopmentFunctions sniff.
 *
 * @since 0.11.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 *
 * @covers \WordPressCS\WordPress\Sniffs\PHP\DevelopmentFunctionsSniff
 */
final class DevelopmentFunctionsUnitTest extends AbstractSniffUnitTest {

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
			7  => 1,
			8  => 1,
			9  => 1,
			10 => 1,
			11 => 1,
			13 => 1,
			15 => 1,
			16 => 1,
			24 => 1,
			33 => 1,
			34 => 1,
			42 => 1,
		);
	}
}
