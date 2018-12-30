<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Arrays;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ArrayIndentation sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.12.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class ArrayIndentationUnitTest extends AbstractSniffUnitTest {

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
		// Tab width setting is only needed for the tabbed file.
		if ( 'ArrayIndentationUnitTest.1.inc' === $testFile ) {
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
			23  => 1,
			24  => 1,
			25  => 1,
			28  => 1,
			29  => 1,
			30  => 1,
			33  => 1,
			34  => 2,
			36  => 1,
			38  => 1,
			39  => 1,
			40  => 1,
			44  => 1,
			45  => 1,
			46  => 1,
			50  => 1,
			51  => 1,
			52  => 1,
			55  => 1,
			56  => 1,
			57  => 1,
			58  => 1,
			60  => 1,
			61  => 1,
			66  => 1,
			80  => 1,
			85  => 1,
			88  => 1,
			103 => 1,
			104 => 1,
			105 => 1,
			111 => 1,
			112 => 1,
			113 => 1,
			120 => 1,
			121 => 1,
			122 => 1,
			132 => 1,
			133 => 1,
			134 => 1,
			140 => 1,
			141 => 1,
			142 => 1,
			151 => 1,
			152 => 1,
			153 => 1,
			156 => 1,
			157 => 1,
			175 => 1,
			179 => 1,
			185 => 1,
			198 => 1,
			202 => 1,
			209 => 1,
			218 => 1,
			219 => 1,
			251 => 1,
			252 => 1,
			253 => 1,
			254 => 1,
			255 => 1,
			260 => 1,
			261 => 1,
			262 => 1,
			263 => 1,
			264 => 1,
			269 => 1,
			271 => 1,
			273 => 1,
			274 => 1,
			279 => 1,
			280 => 1,
			286 => 1,
			287 => 1,
			293 => 1,
			294 => 1,
			300 => 1,
			301 => 1,
			307 => 1,
			308 => 1,
			315 => 1,
			316 => 1,
			323 => 1,
			324 => 1,
			331 => 1,
			332 => 1,
			347 => 1,
			356 => 1,
			369 => 1,
			398 => 1,
			399 => 1,
			416 => 1,
			417 => 2,
			418 => 1,
			420 => 1,
			421 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();
	}

}
