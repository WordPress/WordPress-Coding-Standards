<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
use WordPress\PHPCSHelper;

/**
 * Unit test class for the PrecisionAlignment sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 */
class PrecisionAlignmentUnitTest extends AbstractSniffUnitTest {

	/**
	 * The tab width to use during testing.
	 *
	 * @var int
	 */
	private $tab_width = 4;

	/**
	 * Get a list of CLI values to set before the file is tested.
	 *
	 * Used by PHPCS 2.x.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array
	 */
	public function getCliValues( $testFile ) {
		return array( '--tab-width=' . $this->tab_width );
	}

	/**
	 * Set CLI values before the file is tested.
	 *
	 * Used by PHPCS 3.x.
	 *
	 * @param string                  $testFile The name of the file being tested.
	 * @param \PHP_CodeSniffer\Config $config   The config data for the test run.
	 *
	 * @return void
	 */
	public function setCliValues( $testFile, $config ) {
		$config->tabWidth = $this->tab_width;
	}

	/**
	 * Get a list of all test files to check.
	 *
	 * @param string $testFileBase The base path that the unit tests files will have.
	 *
	 * @return string[]
	 */
	protected function getTestFiles( $testFileBase ) {

		$testFiles = parent::getTestFiles( $testFileBase );

		/*
		 * Testing whether the PHPCS annotations are properly respected is only useful on
		 * PHPCS versions which support the PHPCS annotations.
		 */
		if ( version_compare( PHPCSHelper::get_version(), '3.2.0', '<' ) === true ) {
			$key = array_search( $testFileBase . '5.inc', $testFiles, true );
			if ( false !== $key ) {
				unset( $testFiles[ $key ] );
			}
		}

		return $testFiles;
	}

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
			case 'PrecisionAlignmentUnitTest.1.inc':
				return array(
					20 => 1,
					27 => 1,
					30 => 1,
					31 => 1,
					32 => 1,
					39 => 1,
				);

			case 'PrecisionAlignmentUnitTest.4.inc':
				return array(
					1 => 1, // Will show a `Internal.NoCodeFound` warning in PHP 5.3 with short open tags off.
					2 => ( PHP_VERSION_ID < 50400 && false === (bool) ini_get( 'short_open_tag' ) ) ? 0 : 1,
					3 => ( PHP_VERSION_ID < 50400 && false === (bool) ini_get( 'short_open_tag' ) ) ? 0 : 1,
					4 => ( PHP_VERSION_ID < 50400 && false === (bool) ini_get( 'short_open_tag' ) ) ? 0 : 1,
					5 => ( PHP_VERSION_ID < 50400 && false === (bool) ini_get( 'short_open_tag' ) ) ? 0 : 1,
				);

			case 'PrecisionAlignmentUnitTest.5.inc':
				$warnings = array(
					9  => 1,
					14 => 1,
					19 => 1,
					24 => 0,
					29 => 0,
					34 => 1,
					39 => 1,
					44 => 1,
					54 => 0,
					56 => 0,
					58 => 0,
				);

				/*
				 * The PHPCS 3.2.x versions contained a bug in the selective disable/enable logic
				 * compared to the intended behaviour as documented, which prevented the particular
				 * messages being tested on these lines from being thrown. See upstream issue #1986.
				 */
				if ( version_compare( PHPCSHelper::get_version(), '3.3.0', '>=' ) === true ) {
					$warnings[24] = 1;
					$warnings[29] = 1;
					$warnings[54] = 1;
					$warnings[56] = 1;
					$warnings[58] = 1;
				}

				return $warnings;

			case 'PrecisionAlignmentUnitTest.css':
				return array(
					4 => 1,
				);

			case 'PrecisionAlignmentUnitTest.js':
				return array(
					4 => 1,
					5 => 1,
					6 => 1,
				);

			case 'PrecisionAlignmentUnitTest.2.inc':
			case 'PrecisionAlignmentUnitTest.3.inc':
			default:
				return array();
		}
	}

}
