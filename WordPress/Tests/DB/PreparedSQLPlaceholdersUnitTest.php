<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\DB;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the PreparedSQLPlaceholders sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 */
class PreparedSQLPlaceholdersUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			28  => 5,
			31  => 1,
			32  => 1,
			33  => 2,
			34  => 2,
			39  => 1,
			40  => 1,
			41  => 1,
			54  => 1,
			141 => 1,
			149 => 1,
			151 => 1,
			162 => 1,
			163 => 1,
			164 => 1,
			165 => 1,
			177 => 1,
			179 => 1,
			181 => 1,
			183 => 1,
			185 => 3,
			187 => 3,
			189 => 1,
			191 => 1,
			193 => 1,
			195 => 1,
			197 => 2,
			199 => 1,
			205 => 1,
			207 => 2,
			209 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			12  => 1,
			16  => 1,
			17  => 1,
			23  => 1,
			30  => 2,
			31  => 2,
			32  => 2,
			33  => 1,
			34  => 1,
			44  => 1,
			45  => 1,
			46  => 1,
			55  => 1,
			56  => 1,
			57  => 1,
			58  => 1,
			61  => 1,
			62  => 1, // Whitelist comment deprecation warning.
			66  => 1,
			68  => 1, // Whitelist comment deprecation warning.
			126 => 1,
			139 => 1,
			160 => 2,
			161 => 2,
			177 => 1,
		);
	}

}
