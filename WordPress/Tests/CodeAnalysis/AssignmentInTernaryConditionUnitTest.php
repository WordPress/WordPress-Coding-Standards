<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\CodeAnalysis;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the AssignmentInTernaryCondition sniff.
 *
 * @since 0.14.0
 *
 * @covers \WordPressCS\WordPress\Sniffs\CodeAnalysis\AssignmentInTernaryConditionSniff
 */
final class AssignmentInTernaryConditionUnitTest extends AbstractSniffUnitTest {

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
			12 => 1,
			13 => 1,
			14 => 2,
			15 => 1,
			16 => 1,
			17 => 2,
			18 => 3,
			51 => 2,
			53 => 1,
			54 => 1,
			57 => 1,
		);
	}
}
