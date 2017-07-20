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
					1   => 2, // 2 x error for incorrect prefix passed.
					10  => 1,
					18  => 1,
					21  => 1,
					22  => 1,
					23  => 1,
					24  => 1,
					25  => 1,
					28  => 1,
					30  => 1,
					31  => 1,
					33  => 1,
					34  => 1,
					35  => 1,
					37  => 1,
					38  => 1,
					39  => 1,
					40  => 1,
					90  => 1,
					91  => 1,
					// Scoped.
					149 => ( PHP_VERSION_ID >= 50300 ) ? 0 : 1, // PHPCS on PHP 5.2 does not recognize namespaces.
					151 => ( PHP_VERSION_ID >= 50300 ) ? 0 : 1, // PHPCS on PHP 5.2 does not recognize namespaces.
					153 => ( PHP_VERSION_ID >= 50300 ) ? 0 : 1, // PHPCS on PHP 5.2 does not recognize namespaces.
					154 => ( PHP_VERSION_ID >= 50300 ) ? 0 : 1, // PHPCS on PHP 5.2 does not recognize namespaces.
					155 => ( PHP_VERSION_ID >= 50300 ) ? 0 : 1, // PHPCS on PHP 5.2 does not recognize namespaces.
					// Backfills.
					225 => ( function_exists( 'mb_strpos' ) ) ? 0 : 1,
					230 => ( function_exists( 'array_column' ) ) ? 0 : 1,
					234 => ( defined( 'E_DEPRECATED' ) ) ? 0 : 1,
					238 => ( class_exists( 'IntlTimeZone' ) ) ? 0 : 1,
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
	 * @param string $testFile The name of the file being tested.
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList( $testFile = 'PrefixAllGlobalsUnitTest.inc' ) {

		switch ( $testFile ) {
			case 'PrefixAllGlobalsUnitTest.inc':
				return array(
					249 => 1,
					250 => 1,
					253 => 1,
					254 => 1,
					255 => 1,
					256 => 1,
					257 => 1,
					258 => 1,
					259 => 1,
					260 => 1,
					261 => 1,
					263 => 1,
					264 => 1,
					265 => 1,
					266 => 1,
					267 => 1,
					269 => 1,
					270 => 1,
					271 => 1,
					272 => 1,
					273 => 1,
					274 => 1,
					275 => 1,
					286 => 1,
					287 => 1,
					288 => 1,
					290 => 1,
					291 => 1,
					292 => 1,
					293 => 1,
					295 => 1,
					296 => 1,
					297 => 1,
					299 => 1,
				);

			default:
				return array();

		} // End switch().

	}

} // End class.
