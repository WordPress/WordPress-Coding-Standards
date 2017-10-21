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
			23  => 3,
			25  => 2,
			26  => 2,
			27  => 2,
			28  => 2,
			29  => 2,
			34  => 1,
			35  => 1,
			36  => 1,
			40  => 1,
			41  => 1,
			49  => 1,
			50  => 1,
			51  => 1,
			52  => 1,
			53  => 1,
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
			16  => 1,
			17  => 1,
		);

	}

} // End class.
