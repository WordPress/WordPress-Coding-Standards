<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Arrays;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ArrayKeySpacingRestrictions sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
final class ArrayKeySpacingRestrictionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			4  => 1,
			5  => 1,
			6  => 1,
			11 => 1,
			12 => 1,
			13 => 1,
			16 => 1,
			17 => 1,
			18 => 2,
			23 => 1,
			26 => 1,
			29 => 1,
			33 => 2,
			34 => 1,
			35 => 1,
			40 => 2,
			41 => 2,
			45 => 1,
			46 => 1,
			51 => 1,
			58 => 1,
			59 => 1,
			60 => 1,
			66 => 1,
			67 => 1,
			74 => 1,
			75 => 1,
			76 => 1,
			82 => 1,
			83 => 1,
			87 => 1,
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
