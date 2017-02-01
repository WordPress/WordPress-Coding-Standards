<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the AdminBarRemoval sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.3.0
 */
class WordPress_Tests_VIP_AdminBarRemovalUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {

		switch ( $testFile ) {
			case 'AdminBarRemovalUnitTest.inc':
				return array(
					3   => 1,
					6   => 1,
					9   => 1,
					12  => 1,
					13  => 1,
					19  => 1,
					20  => 1,
					21  => 1,
					26  => 1,
					32  => 1,
					56  => ( PHP_VERSION_ID >= 50300 ) ? 1 : 0, // PHPCS on PHP 5.2 does not recognize T_NOWDOC.
					57  => ( PHP_VERSION_ID >= 50300 ) ? 1 : 0, // PHPCS on PHP 5.2 does not recognize T_NOWDOC.
					58  => ( PHP_VERSION_ID >= 50300 ) ? 1 : 0, // PHPCS on PHP 5.2 does not recognize T_NOWDOC.
					68  => 1,
					69  => 1,
					70  => 1,
					81  => 1,
					82  => 1,
					83  => 1,
					92  => 1,
					103 => 1,
					104 => 1,
					105 => 1,
				);

			case 'AdminBarRemovalUnitTest.css':
				return array(
					15  => 1,
					16 => 1,
					17 => 1,
					22 => 1,
					23 => 1,
					24 => 1,
					29 => 1,
					30 => 1,
					31 => 1,
					38 => 1,
					39 => 1,
					40 => 1,
					46 => 1,
					47 => 1,
					48 => 1,
				);

			default:
				return array();

		} // End switch().
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();

	}

} // End class.
