<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\WP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the EnqueuedCheck sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   1.0.0
 */
final class EnqueuedResourceParametersUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			6  => 1,
			9  => 1,
			10 => 1,
			12 => 1,
			13 => 1,
			14 => 1,
			22 => 1,
			54 => 1,
			57 => 1,
			61 => 1,
			82 => 1,
			85 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			3  => 2,
			11 => 1,
			32 => 1,
			39 => 2,
			42 => 1,
			45 => 1,
			66 => 2,
			77 => 1,
		);
	}
}
