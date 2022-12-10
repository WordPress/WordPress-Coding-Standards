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
 * Unit test class for the EnqueuedResources sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
final class EnqueuedResourcesUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'EnqueuedResourcesUnitTest.1.inc':
				return array(
					1  => 1,
					2  => 1,
					6  => 1,
					7  => 1,
					10 => 1,
					11 => 1,
					13 => 1,
					14 => 1,
					16 => 1,
					17 => 1,
					20 => 1,
					21 => 1,
					25 => 1,
					26 => 1,
					30 => 1,
					31 => 1,
					42 => 1,
					46 => 1,
					48 => 1,
					49 => 1,
					54 => 1,
					55 => 1,
				);

			case 'EnqueuedResourcesUnitTest.2.inc':
				// These tests will only yield reliable results when PHPCS is run on PHP 7.3 or higher.
				if ( \PHP_VERSION_ID < 70300 ) {
					return array();
				}

				return array(
					7  => 1,
					8  => 1,
					12 => 1,
					13 => 1,
					17 => 1,
					18 => 1,
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
