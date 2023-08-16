<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ControlStructureSpacing sniff.
 *
 * @since 2013-06-11
 * @since 0.13.0     Class name changed: this class is now namespaced.
 *
 * @covers \WordPressCS\WordPress\Sniffs\WhiteSpace\ControlStructureSpacingSniff
 */
final class ControlStructureSpacingUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList( $testFile = '' ) {

		switch ( $testFile ) {
			case 'ControlStructureSpacingUnitTest.1.inc':
				$ret = array(
					4   => 2,
					17  => 2,
					29  => 5,
					37  => 1,
					41  => 1,
					42  => 1,
					50  => 2,
					52  => 5,
					59  => 1,
					67  => 2,
					94  => 1,
					96  => 1,
					102 => 1,
					104 => 1,
					108 => 2,
					112 => 2,
					159 => 1,
					169 => 1,
					179 => 1,
					195 => 1,
					203 => 2,
					212 => 1,
					222 => 5,
					228 => 1,
					232 => 1,
					241 => 1,
				);

				return $ret;

			case 'ControlStructureSpacingUnitTest.2.inc':
				return array(
					6  => 1,
					16 => 1,
					19 => 1,
				);

			default:
				return array();
		}
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
