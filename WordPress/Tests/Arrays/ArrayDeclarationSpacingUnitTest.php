<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Arrays;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ArrayDeclarationSpacing sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * @covers \WordPressCS\WordPress\Sniffs\Arrays\ArrayDeclarationSpacingSniff
 */
final class ArrayDeclarationSpacingUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {

		switch ( $testFile ) {
			// Long arrays.
			case 'ArrayDeclarationSpacingUnitTest.1.inc':
				return array(
					9  => 4,
					13 => 2,
					15 => 1,
					19 => 1,
					22 => 1,
					25 => 1,
					44 => 1,
					45 => 1,
					48 => 1,
					49 => 1,
					52 => 2,
					54 => 1,
					57 => 1,
					58 => 1,
					62 => 1,
					63 => 1,
					75 => 1,
				);

			// Short arrays.
			case 'ArrayDeclarationSpacingUnitTest.2.inc':
				return array(
					9  => 4,
					13 => 2,
					15 => 1,
					19 => 1,
					22 => 1,
					25 => 1,
					44 => 1,
					45 => 1,
					48 => 1,
					49 => 1,
					52 => 2,
					54 => 1,
					57 => 1,
					58 => 1,
					62 => 1,
					63 => 1,
					75 => 1,
					97 => 1,
				);

			default:
				return array();
		}
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();
	}
}
