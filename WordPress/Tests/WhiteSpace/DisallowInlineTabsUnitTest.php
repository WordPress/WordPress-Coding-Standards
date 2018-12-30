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
 * Unit test class for the DisallowInlineTabs sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.12.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class DisallowInlineTabsUnitTest extends AbstractSniffUnitTest {

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
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			17 => 1,
			22 => 1,
			23 => 1,
			24 => 1,
			26 => 1,
			32 => 1,
			33 => 1,
			34 => 1,
			35 => 1,
			36 => 1,
			37 => 1,
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
