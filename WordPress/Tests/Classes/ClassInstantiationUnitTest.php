<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the ClassInstantiation sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.12.0
 */
class WordPress_Tests_Classes_ClassInstantiationUnitTest extends AbstractSniffUnitTest {

	/**
	 * Get a list of all test files to check.
	 *
	 * @param string $testFileBase The base path that the unit tests files will have.
	 *
	 * @return string[]
	 */
	protected function getTestFiles( $testFileBase ) {
		$testFiles = parent::getTestFiles( $testFileBase );

		if ( PHP_VERSION_ID < 50300 ) {
			$testFiles = array_diff( $testFiles, array( $testFileBase . '2.inc' ) );
		}

		return $testFiles;
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {

		switch ( $testFile ) {
			case 'ClassInstantiationUnitTest.1.inc':
				return array(
					37 => 1,
					38 => 1,
					39 => 1,
					40 => 1,
					41 => 1,
					42 => 1,
					46 => 1,
					50 => 1,
					56 => 1,
					61 => 1,
					62 => 1,
					67 => 1,
					69 => 1,
					71 => 1,
					72 => 1,
					75 => 1,
					77 => 1,
					79 => 1,
					80 => 1,
					84 => 1,
					85 => 1,
				);

			case 'ClassInstantiationUnitTest.2.inc':
				return array(
					16 => 1,
					17 => 1,
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

} // End class.
