<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the PrefixAllGlobals sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.12.0
 */
class WordPress_Tests_NamingConventions_PrefixAllGlobalsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = 'PrefixAllGlobalsUnitTest.inc' ) {

		switch ( $testFile ) {
			case 'PrefixAllGlobalsUnitTest.inc':
				return array(
					1   => 2, // 2 x error for incorrect prefix.
					10  => 1,
					18  => 1,
					21  => 1,
					22  => 1,
					25  => 1,
					27  => 1,
					28  => 1,
					30  => 1,
					31  => 1,
					32  => 1,
					34  => 1,
					35  => 1,
					79  => 1,
					80  => 1,
					134 => ( PHP_VERSION_ID >= 50300 ) ? 0 : 1, // PHPCS on PHP 5.2 does not recognize namespaces.
					136 => ( PHP_VERSION_ID >= 50300 ) ? 0 : 1, // PHPCS on PHP 5.2 does not recognize namespaces.
					138 => ( PHP_VERSION_ID >= 50300 ) ? 0 : 1, // PHPCS on PHP 5.2 does not recognize namespaces.
					139 => ( PHP_VERSION_ID >= 50300 ) ? 0 : 1, // PHPCS on PHP 5.2 does not recognize namespaces.
					140 => ( PHP_VERSION_ID >= 50300 ) ? 0 : 1, // PHPCS on PHP 5.2 does not recognize namespaces.
					209 => ( function_exists( 'mb_strpos' ) ) ? 0 : 1,
					214 => ( function_exists( 'array_column' ) ) ? 0 : 1,
					218 => ( defined( 'E_DEPRECATED' ) ) ? 0 : 1,
					222 => ( class_exists( 'IntlTimeZone' ) ) ? 0 : 1,
				);

			case 'PrefixAllGlobalsUnitTest.1.inc':
				// Namespaced - all OK.
				if ( PHP_VERSION_ID >= 50300 ) {
					return array();
				}

				// PHPCS on PHP 5.2 does not recognize namespaces.
				return array(
					9  => 1,
					11 => 1,
					13 => 1,
					14 => 1,
					15 => 1,
				);

			default:
				return array();

		} // End switch().

	} // end getErrorList()

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();

	}

} // End class.
