<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the CapitalPDangit sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.12.0
 */
class WordPress_Tests_WP_CapitalPDangitUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array();

	} // end getErrorList()

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList( $testFile = 'CapitalPDangitUnitTest.inc' ) {

		switch ( $testFile ) {
			case 'CapitalPDangitUnitTest.inc':
				return array(
					3   => 1,
					5   => 1,
					8   => 1,
					26  => 1,
					28  => 1,
					34  => 1,
					35  => 1,
					36  => 1,
					40  => 1,
					41  => 1,
					45  => 1,
					53  => 1,
					60  => 1,
					61  => 1,
					62  => 1,
					65  => 1,
					66  => 1,
					68  => ( PHP_VERSION_ID >= 50300 ) ? 1 : 2, // PHPCS on PHP 5.2 apparently breaks the token up into two tokens.
					101 => 1,
					139 => 1,
					146 => 0, // False negative.
					173 => 1,
				);

			case 'CapitalPDangitUnitTest.1.inc':
				return array(
					9 => ( PHP_VERSION_ID >= 50300 ) ? 1 : 0, // PHPCS on PHP 5.2 does not recognize nowdocs.
				);

			default:
				return array();

		} // End switch().
	}

} // End class.
