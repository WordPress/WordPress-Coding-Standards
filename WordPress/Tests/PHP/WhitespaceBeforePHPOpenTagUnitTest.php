<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the WhitespaceBeforePHPOpenTag sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   1.0.0
 */
class WhitespaceBeforePHPOpenTagUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array<int, int>
	 */
	public function getErrorList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'WhitespaceBeforePHPOpenTagUnitTest.4.inc':
			case 'WhitespaceBeforePHPOpenTagUnitTest.5.inc':
			case 'WhitespaceBeforePHPOpenTagUnitTest.6.inc':
			case 'WhitespaceBeforePHPOpenTagUnitTest.7.inc':
				return array(
					1 => 1,
				);

			default:
				return array();
		}
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array<int, int>
	 */
	public function getWarningList() {
		return array();
	}

}
