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
 * Unit test class for the GlobalVariables sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  This sniff has been moved from the `Variables` category to the `WP`
 *                 category and renamed from `GlobalVariables` to `GlobalVariablesOverride`.
 */
class GlobalVariablesOverrideUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'GlobalVariablesOverrideUnitTest.1.inc':
				return array(
					3   => 1,
					6   => 1,
					8   => 1,
					16  => 1,
					17  => 1,
					18  => 1,
					25  => 1,
					35  => 1,
					36  => 1,
					54  => 1,
					95  => 1,
					128 => 1,
					133 => 1,
					139 => 1,
					140 => 1,
					141 => 2,
					142 => 1,
					143 => 1,
					146 => 1,
					181 => 1,
					198 => 1,
					212 => 4,
				);

			case 'GlobalVariablesOverrideUnitTest.2.inc':
				return array(
					12 => 1,
					13 => 1,
					16 => 1,
					17 => 1,
					18 => 2,
					19 => 1,
					20 => 1,
					23 => 1,
					27 => 1,
					29 => 1,
				);

			case 'GlobalVariablesOverrideUnitTest.3.inc':
				return array(
					29 => 1,
				);

			default:
				return array();
		}
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'GlobalVariablesOverrideUnitTest.1.inc':
				return array(
					11 => 1, // Whitelist comment deprecation warning.
				);

			default:
				return array();
		}
	}

}
