<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPress\Tests\NamingConventions
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\NamingConventions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ValidTestClassName sniff.
 *
 * @package WPCS\WordPress\Tests\NamingConventions
 * @since   3.0.0
 */
class ValidTestClassNameUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'InvalidTestClassTest.php':
				return array(
					9  => 1, // Class name doesn't match filename
					22 => 1, // Class name doesn't end with Test
					35 => 2, // Invalid class name format (snake_case) and doesn't match filename
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
