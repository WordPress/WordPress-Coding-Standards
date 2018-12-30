<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\WP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the WP_DiscouragedConstants sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.14.0
 */
class DiscouragedConstantsUnitTest extends AbstractSniffUnitTest {

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
			63 => 1,
			64 => 1,
			66 => 1,
			67 => 1,
			71 => 1,
			72 => 1,
		);
	}

}
