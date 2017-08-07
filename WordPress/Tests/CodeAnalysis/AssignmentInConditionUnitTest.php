<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\CodeAnalysis;

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
			47 => 1,
			48 => 1,
			49 => 1,
			50 => 1,
			51 => 1,
			52 => 1,
			53 => 1,
			54 => 1,
			55 => 1,
			56 => 1,
			57 => 1,
			58 => 1,
			59 => 1,
			60 => 1,
			61 => 1,
			62 => 2,
			64 => 1,
			65 => 1,
			68 => 1,
			69 => 1,
			70 => 1,
			71 => 1,
			72 => 1,
			73 => 1,
			74 => 1,
			76 => 1,
			78 => 1,
			81 => 2,
			85 => 1,
			86 => 2,
			89 => 1,
			91 => 1,
			93 => 1,
		);

	}

} // End class.
