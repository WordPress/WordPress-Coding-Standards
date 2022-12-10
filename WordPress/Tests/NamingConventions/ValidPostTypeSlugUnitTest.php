<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\NamingConventions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the PostType sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   2.2.0
 */
final class ValidPostTypeSlugUnitTest extends AbstractSniffUnitTest {

	/**
	 * Set warnings level to 3 to trigger suggestions as warnings.
	 *
	 * @param string                  $filename The name of the file being tested.
	 * @param \PHP_CodeSniffer\Config $config   The config data for the run.
	 *
	 * @return void
	 */
	public function setCliValues( $filename, $config ) {
		$config->warningSeverity = 3;
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'ValidPostTypeSlugUnitTest.1.inc':
				return array(
					5  => 1,
					6  => 1,
					7  => 1,
					8  => 1,
					20 => 1,
					36 => 1,
					37 => 1,
					39 => 1,
					40 => 1,
					49 => 1,
					50 => 2,
					52 => 1,
					62 => 1,
					64 => 1,
				);

			case 'ValidPostTypeSlugUnitTest.2.inc':
				// These tests will only yield reliable results when PHPCS is run on PHP 7.3 or higher.
				if ( \PHP_VERSION_ID < 70300 ) {
					return array();
				}

				return array(
					17 => 1,
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
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'ValidPostTypeSlugUnitTest.1.inc':
				return array(
					24 => 1,
					27 => 1,
					28 => 1,
					29 => 1,
					30 => 1,
					31 => 1,
					33 => 1,
					34 => 1,
					45 => 1,
					49 => 1,
					55 => 1,
					56 => 1,
					67 => 1,
				);

			case 'ValidPostTypeSlugUnitTest.2.inc':
				// These tests will only yield reliable results when PHPCS is run on PHP 7.3 or higher.
				if ( \PHP_VERSION_ID < 70300 ) {
					return array();
				}

				return array(
					7 => 1,
				);

			default:
				return array();
		}
	}
}
