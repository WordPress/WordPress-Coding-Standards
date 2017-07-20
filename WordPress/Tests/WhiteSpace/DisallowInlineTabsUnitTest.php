<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the DisallowInlineTabs sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.12.0
 */
class WordPress_Tests_WhiteSpace_DisallowInlineTabsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Get a list of CLI values to set before the file is tested.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array
	 */
	public function getCliValues( $testFile ) {
		return array( '--tab-width=4' );
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

} // End class.
