<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\DB;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the PreparedSQLPlaceholders sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 */
class PreparedSQLPlaceholdersUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			17  => 3,
			19  => 2,
			20  => 2,
			21  => 2,
			26  => 1,
			27  => 1,
			28  => 1,
			32  => 1,
			33  => 1,
			41  => 1,
			42  => 1,
			43  => 1,
			44  => 1,
			45  => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			12  => 1,
		);

	}

} // End class.
