<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\Arrays;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the Arrays.MultipleStatementAlignment sniff.
 *
 * The unit test class uses two test files to cover all possibilities:
 * 1. Tab-based indentation, long arrays.
 * 2. Space-based indentation, short arrays.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 */
class MultipleStatementAlignmentUnitTest extends AbstractSniffUnitTest {

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
		// Tab width setting is only needed for the tabbed file.
		if ( 'MultipleStatementAlignmentUnitTest.1.inc' === $testFile ) {
			return array( '--tab-width=' . $this->tab_width );
		}

		return array();
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
		// Tab width setting is only needed for the tabbed file.
		if ( 'MultipleStatementAlignmentUnitTest.1.inc' === $testFile ) {
			$config->tabWidth = $this->tab_width;
		} else {
			$config->tabWidth = 0;
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
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			8   => 2,
			11  => 1,
			14  => 2,
			42  => 1,
			45  => 1,
			46  => 1,
			47  => 1,
			54  => 1,
			57  => 1,
			59  => 1,
			66  => 1,
			67  => 1,
			68  => 1,
			85  => 1,
			87  => 1,
			92  => 1,
			110 => 1,
			112 => 1,
			124 => 1,
			127 => 1,
			129 => 1,
			131 => 1,
			134 => 1,
			135 => 1,
			138 => 1,
			141 => 1,
			145 => 1,
			166 => 1,
			168 => 1,
			169 => 1,
			172 => 1,
			173 => 1,
			174 => 1,
			175 => 1,
			200 => 1,
			205 => 1,
			211 => 1,
			215 => 1,
			216 => 1,
			217 => 1,
			233 => 1,
			235 => 1,
			240 => 1,
			243 => 1,
			245 => 1,
			250 => 1,
			251 => 1,
			253 => 1,
			255 => 1,
			258 => 1,
			259 => 1,
			260 => 1,
			263 => 1,
			265 => 1,
			274 => 1,
			275 => 1,
			284 => 1,
			286 => 1,
			287 => 1,
			289 => 1,
			292 => 1,
			293 => 1,
		);
	}

} // End class.
