<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\WP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the EnqueuedResources sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since
 */
class ExpectedSlashedUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @since
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			6   => 1,
			9   => 1,
			16  => 1,
			17  => 1,
			19  => 1,
			22  => 1,
			31  => 2,
			32  => 1,
			36  => 1,
			41  => 1,
			44  => 1,
			45  => 1,
			57  => 1,
			60  => 1,
			61  => 2,
			92  => 1,
			93  => 1,
			114 => 1,
			116 => 1,
			118 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @since
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			55  => 1,
			56  => 1,
			59  => 1,
			66  => 2,
			70  => 1,
			103 => 1,
			105 => 1,
			108 => 1,
			111 => 1,
			114 => 1,
			116 => 1,
			118 => 1,
			131 => 1,
			133 => 1,
		);
	}

} // End class.
