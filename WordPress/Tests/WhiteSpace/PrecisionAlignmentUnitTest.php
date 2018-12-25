<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

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
	 * Set CLI values before the file is tested.
	 *
	 * @param string                  $testFile The name of the file being tested.
	 * @param \PHP_CodeSniffer\Config $config   The config data for the test run.
	 *
	 * @return void
	 */
	public function setCliValues( $testFile, $config ) {
		$config->tabWidth = $this->tab_width;

		// Testing a file with "--ignore-annotations".
		if ( 'PrecisionAlignmentUnitTest.6.inc' === $testFile ) {
			$config->annotations = false;
		}
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
					34 => 1, // Whitelist comment deprecation warning.
					39 => 1,
					65 => 1,
				);

			case 'PrecisionAlignmentUnitTest.4.inc':
				return array(
					1 => 1,
					2 => 1,
					3 => 1,
					4 => 1,
					5 => 1,
				);

			case 'PrecisionAlignmentUnitTest.5.inc':
				return array(
					9  => 1,
					14 => 1,
					19 => 1,
					24 => 1,
					29 => 1,
					34 => 1,
					39 => 1,
					44 => 1,
					54 => 1,
					56 => 1,
					58 => 1,
				);

			case 'PrecisionAlignmentUnitTest.6.inc':
				return array(
					4 => 1,
				);

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
