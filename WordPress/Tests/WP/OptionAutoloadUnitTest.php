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
 * Unit test class for the OptionAutoload sniff.
 *
 * @since 3.2.0
 *
 * @covers \WordPressCS\WordPress\Sniffs\WP\OptionAutoloadSniff
 */
final class OptionAutoloadUnitTest extends AbstractSniffUnitTest {

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
			96  => 1,
			97  => 1,
			98  => 1,
			99  => 1,
			100 => 1,
			105 => 1,
			107 => 1,
			108 => 1,
			109 => 1,
			110 => 1,
			111 => 1,
			112 => 1,
			113 => 1,
			114 => 1,
			115 => 1,
			116 => 1,
			117 => 1,
			118 => 1,
			119 => 1,
			120 => 1,
			121 => 1,
			122 => 1,
			123 => 1,
			124 => 1,
			125 => 1,
			126 => 1,
			127 => 1,
			128 => 1,
			129 => 1,
			132 => 1,
			133 => 1,
			134 => 1,
			135 => 1,
			136 => 1,
			137 => 1,
			138 => 1,
			139 => 1,
			140 => 1,
			141 => 1,
			142 => 1,
			143 => 1,
			148 => 1,
			149 => 1,
			150 => 1,
			151 => 1,
			152 => 1,
			153 => 1,
			158 => 1,
			159 => 1,
			160 => 1,
			161 => 1,
			162 => 1,
			163 => 1,
			164 => 1,
			170 => 1,
			177 => 1,
			179 => 1,
			180 => 1,
			181 => 1,
			182 => 1,
			183 => 1,
			184 => 1,
			185 => 1,
			187 => 1,
			188 => 1,
			189 => 1,
			190 => 1,
			193 => 1,
			194 => 1,
			195 => 1,
			196 => 1,
			201 => 1,
			207 => 1,
			209 => 1,
			212 => 1,
		);
	}
}
