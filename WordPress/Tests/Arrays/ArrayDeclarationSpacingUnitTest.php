<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
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
 */
class ArrayDeclarationSpacingUnitTest extends AbstractSniffUnitTest {

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
					8   => 2,
					11  => 2,
					16  => 4,
					20  => 2,
					22  => 1,
					26  => 1,
					29  => 1,
					32  => 1,
					38  => 1,
					39  => 1,
					45  => 1,
					49  => 1,
					62  => 2,
					63  => 1,
					64  => 1,
					84  => 1,
					85  => 1,
					88  => 1,
					89  => 2,
					92  => 3,
					94  => 1,
					97  => 1,
					98  => 2,
					101 => 1,
					103 => 2,
					104 => 2,
					108 => 1,
					109 => 1,
					121 => 1,
				);

			// Short arrays.
			case 'ArrayDeclarationSpacingUnitTest.2.inc':
				return array(
					8   => 2,
					11  => 2,
					16  => 4,
					20  => 2,
					22  => 1,
					26  => 1,
					29  => 1,
					32  => 1,
					45  => 1,
					46  => 1,
					47  => 1,
					67  => 1,
					68  => 1,
					71  => 1,
					72  => 2,
					75  => 3,
					77  => 1,
					80  => 1,
					81  => 2,
					84  => 1,
					86  => 2,
					87  => 2,
					91  => 1,
					92  => 1,
					104 => 1,
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
