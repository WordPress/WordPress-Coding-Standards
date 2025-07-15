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
					106 => 1,
					107 => 1,
					108 => 1,
					109 => 1,
					110 => 1,
					115 => 1,
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
					133 => 1,
					135 => 1,
					136 => 1,
					137 => 1,
					138 => 1,
					139 => 1,
					140 => 1,
					141 => 1,
					142 => 1,
					143 => 1,
					147 => 1,
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
					163 => 1,
					164 => 1,
					165 => 1,
					166 => 1,
					167 => 1,
					168 => 1,
					173 => 1,
					174 => 1,
					175 => 1,
					176 => 1,
					177 => 1,
					178 => 1,
					179 => 1,
					185 => 1,
					192 => 1,
					194 => 1,
					195 => 1,
					196 => 1,
					197 => 1,
					198 => 1,
					199 => 1,
					200 => 1,
					202 => 1,
					203 => 1,
					204 => 1,
					205 => 1,
					208 => 1,
					209 => 1,
					210 => 1,
					211 => 1,
					216 => 1,
					222 => 1,
					224 => 1,
					227 => 1,
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
