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
					107 => 1,
					108 => 1,
					109 => 1,
					110 => 1,
					111 => 1,
					116 => 1,
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
					130 => 1,
					134 => 1,
					136 => 1,
					137 => 1,
					138 => 1,
					139 => 1,
					140 => 1,
					141 => 1,
					142 => 1,
					143 => 1,
					144 => 1,
					148 => 1,
					149 => 1,
					150 => 1,
					151 => 1,
					152 => 1,
					153 => 1,
					154 => 1,
					155 => 1,
					156 => 1,
					157 => 1,
					158 => 1,
					159 => 1,
					164 => 1,
					165 => 1,
					166 => 1,
					167 => 1,
					168 => 1,
					169 => 1,
					174 => 1,
					175 => 1,
					176 => 1,
					177 => 1,
					178 => 1,
					179 => 1,
					180 => 1,
					186 => 1,
					193 => 1,
					195 => 1,
					196 => 1,
					197 => 1,
					198 => 1,
					199 => 1,
					200 => 1,
					201 => 1,
					203 => 1,
					204 => 1,
					205 => 1,
					206 => 1,
					209 => 1,
					210 => 1,
					211 => 1,
					212 => 1,
					217 => 1,
					223 => 1,
					225 => 1,
					228 => 1,
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
