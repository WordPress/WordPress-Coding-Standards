<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\CodeAnalysis;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the EmptyStatement sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.12.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class EmptyStatementUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array();
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'EmptyStatementUnitTest.1.inc':
				return array(
					9  => 1,
					12 => 1,
					15 => 1,
					18 => 1,
					21 => 1,
					22 => 1,
					31 => 1,
					33 => 1,
					43 => 1,
					45 => 1,
				);

			case 'EmptyStatementUnitTest.2.inc':
				return array(
					1 => 1, // Internal.NoCode warning when short open tags is off, otherwise EmptyStatement warning.
					2 => 1,
				);

			default:
				return array();
		}
	}

}
