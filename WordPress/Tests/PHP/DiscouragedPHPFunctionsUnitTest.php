<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the PHP_DiscouragedPHPFunctions sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.11.0
 */
class WordPress_Tests_PHP_DiscouragedPHPFunctionsUnitTest extends AbstractSniffUnitTest {

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
			5  => 1,
			9  => 1,
			10 => 1,
			12 => 1,
			15 => 1,
			16 => 1,
			17 => 1,
			18 => 1,
			19 => 1,
			20 => 1,
			21 => 1,
			22 => 1,
			23 => 1,
			24 => 1,
			25 => 1,
			28 => 1,
			29 => 1,
			30 => 1,
			31 => 1,
			32 => 1,
			35 => 1,
			36 => 1,
			37 => 1,
			38 => 1,
			39 => 1,
			40 => 1,
		);

	}

} // End class.
