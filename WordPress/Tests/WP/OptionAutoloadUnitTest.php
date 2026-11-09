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
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'OptionAutoloadUnitTest.1.inc':
				return array(
					113 => 1,
					114 => 1,
					115 => 1,
					116 => 1,
					117 => 1,
					122 => 1,
					124 => 1,
					125 => 1,
					126 => 1,
					127 => 1,
					128 => 1,
					129 => 1,
					130 => 1,
					131 => 1,
					132 => 1,
					133 => 1,
					134 => 1,
					135 => 1,
					136 => 1,
					140 => 1,
					142 => 1,
					143 => 1,
					144 => 1,
					145 => 1,
					146 => 1,
					147 => 1,
					148 => 1,
					149 => 1,
					150 => 1,
					154 => 1,
					155 => 1,
					156 => 1,
					157 => 1,
					158 => 1,
					159 => 1,
					160 => 1,
					161 => 1,
					162 => 1,
					163 => 1,
					164 => 1,
					165 => 1,
					170 => 1,
					171 => 1,
					172 => 1,
					173 => 1,
					174 => 1,
					175 => 1,
					180 => 1,
					181 => 1,
					182 => 1,
					183 => 1,
					184 => 1,
					185 => 1,
					186 => 1,
					192 => 1,
					199 => 1,
					201 => 1,
					202 => 1,
					203 => 1,
					204 => 1,
					205 => 1,
					206 => 1,
					207 => 1,
					209 => 1,
					210 => 1,
					211 => 1,
					212 => 1,
					215 => 1,
					216 => 1,
					217 => 1,
					218 => 1,
					223 => 1,
					229 => 1,
					231 => 1,
					234 => 1,
				);

			case 'OptionAutoloadUnitTest.3.inc':
				return array(
					8 => 1,
				);

			default:
				return array();
		}
	}
}
