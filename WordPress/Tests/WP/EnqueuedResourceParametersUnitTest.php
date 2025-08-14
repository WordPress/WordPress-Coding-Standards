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
 * Unit test class for the EnqueuedCheck sniff.
 *
 * @since 1.0.0
 *
 * @covers \WordPressCS\WordPress\Sniffs\WP\EnqueuedResourceParametersSniff
 */
final class EnqueuedResourceParametersUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'EnqueuedResourceParametersUnitTest.1.inc':
				return array(
					6   => 1,
					9   => 1,
					10  => 1,
					12  => 1,
					13  => 1,
					14  => 1,
					22  => 1,
					54  => 1,
					57  => 1,
					61  => 1,
					82  => 1,
					85  => 1,
					89  => 1,
					92  => 1,
					95  => 1,
					97  => 1,
					106 => 1,
				);

			case 'EnqueuedResourceParametersUnitTest.2.inc':
				return array(
					5 => 1,
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
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'EnqueuedResourceParametersUnitTest.1.inc':
				return array(
					3   => 2,
					11  => 1,
					32  => 1,
					39  => 2,
					42  => 1,
					45  => 1,
					66  => 2,
					77  => 1,
					100 => 1,
					107 => 1,
					108 => 1,
				);

			default:
				return array();
		}
	}
}
