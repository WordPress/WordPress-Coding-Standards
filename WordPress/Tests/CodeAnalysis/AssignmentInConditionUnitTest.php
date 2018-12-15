<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\CodeAnalysis;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the AssignmentInCondition sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 */
class AssignmentInConditionUnitTest extends AbstractSniffUnitTest {

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
			29  => 1,
			30  => 1,
			31  => 1,
			32  => 1,
			33  => 1,
			34  => 1,
			35  => 1,
			36  => 1,
			37  => 1,
			38  => 1,
			39  => 1,
			40  => 1,
			41  => 1,
			42  => 1,
			43  => 1,
			44  => 2,
			46  => 1,
			47  => 1,
			50  => 1,
			51  => 1,
			52  => 1,
			53  => 1,
			54  => 1,
			55  => 1,
			56  => 1,
			58  => 1,
			60  => 1,
			63  => 2,
			67  => 1,
			68  => 2,
			71  => 1,
			73  => 1,
			75  => 1,
			79  => 1,
			80  => 1,
			81  => 1,
			83  => 1,
			84  => 1,
			86  => 1,
			87  => 1,
			88  => 1,
			91  => 1,
			95  => 1,
			106 => 1,
			107 => 1,
			108 => 2,
			109 => 1,
			110 => 1,
			111 => 2,
			112 => 3,
			141 => 1,
			142 => 1,
			149 => 1,
			150 => 1,
		);
	}

}
