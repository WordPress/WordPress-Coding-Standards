<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\NamingConventions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ValidVariableName sniff.
 *
 * @since 0.9.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 *
 * @covers \WordPressCS\WordPress\Helpers\SnakeCaseHelper
 * @covers \WordPressCS\WordPress\Sniffs\NamingConventions\ValidVariableNameSniff
 */
final class ValidVariableNameUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList() {
		return array(
			2   => 1,
			5   => 1,
			8   => 1,
			11  => 1,
			13  => 1,
			16  => 1,
			18  => 1,
			21  => 1,
			23  => 1,
			26  => 1,
			29  => 1,
			32  => 1,
			34  => 1,
			38  => 1,
			40  => 1,
			43  => 1,
			51  => 1,
			54  => 1,
			55  => 1,
			57  => 1,
			60  => 1,
			61  => 1,
			64  => 1,
			65  => 1,
			70  => 1,
			77  => 1,
			78  => 1,
			79  => 1,
			80  => 1,
			82  => 1,
			86  => 1,
			87  => 1,
			88  => 1,
			99  => 1,
			100 => 1,
			101 => 1,
			102 => 1,
			103 => 1,
			104 => 1,
			105 => 1,
			121 => 1,
			126 => 1,
			138 => 1,
			145 => 1,
			160 => 1,
			172 => 2,
			173 => 1,
			175 => 1,
			176 => 1,
			177 => 1,
			181 => 1,
			182 => 1,
			184 => 1,
			186 => 1,
			190 => 1,
			194 => 1,
			199 => 1,
			200 => 1,
			202 => 1,
			204 => 1,
			211 => 2,
			212 => 2,
			213 => 2,
			216 => 1,
			219 => 1,
			225 => 1,
			227 => 1,
			238 => function_exists( 'mb_strtolower' ) ? 1 : 0,
			239 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList() {
		return array();
	}
}
