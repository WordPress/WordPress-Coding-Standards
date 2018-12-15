<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Classes;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ClassInstantiation sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.12.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class ClassInstantiationUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {

		switch ( $testFile ) {
			case 'ClassInstantiationUnitTest.inc':
				return array(
					37  => 1,
					38  => 1,
					39  => 1,
					40  => 1,
					41  => 1,
					42  => 1,
					46  => 1,
					50  => 1,
					56  => 1,
					61  => 1,
					62  => 1,
					67  => 1,
					69  => 1,
					71  => 1,
					72  => 1,
					75  => 1,
					77  => 1,
					79  => 1,
					80  => 1,
					84  => 1,
					85  => 1,
					97  => 1,
					98  => 1,
					105 => 1,
					106 => 1,
				);

			case 'ClassInstantiationUnitTest.js':
				return array(
					2 => 1,
					3 => 1,
					4 => 1,
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
