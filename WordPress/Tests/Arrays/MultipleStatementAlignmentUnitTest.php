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
		return array(
			1 => 3, // Invalid property value error.
		);
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
			113 => 1,
			115 => 1,
			127 => 1,
			130 => 1,
			132 => 1,
			134 => 1,
			137 => 1,
			138 => 1,
			141 => 1,
			144 => 1,
			148 => 1,
			169 => 1,
			171 => 1,
			172 => 1,
			175 => 1,
			176 => 1,
			177 => 1,
			178 => 1,
			203 => 1,
			208 => 1,
			214 => 1,
			218 => 1,
			219 => 1,
			220 => 1,
			236 => 1,
			238 => 1,
			243 => 1,
			246 => 1,
			248 => 1,
			253 => 1,
			254 => 1,
			256 => 1,
			258 => 1,
			261 => 1,
			262 => 1,
			263 => 1,
			266 => 1,
			268 => 1,
			279 => 1,
			280 => 1,
			289 => 1,
			291 => 1,
			292 => 1,
			294 => 1,
			297 => 1,
			298 => 1,
			317 => 1,
			320 => 1,
			324 => 1,
			373 => 1,
			376 => 1,
			380 => 1,
			416 => 1,
			417 => 1,
			418 => 1,
			420 => 1,
			427 => 1,
			428 => 1,
			430 => 1,
			441 => 1,
			450 => 1,
			457 => 1,
			461 => 1,
			485 => 1,
			486 => 1,
			487 => 1,
			488 => 1,
			491 => 1,
			495 => 1,
			501 => 1,
			503 => 1,
			506 => 1,
			515 => 1,
			522 => 1,
			529 => 1,
			530 => 1,
			531 => 1,
			532 => 1,
			540 => 1,
			542 => 1,
			545 => 1,
			563 => 1,
			566 => 1,
			570 => 1,
		);
	}

} // End class.
