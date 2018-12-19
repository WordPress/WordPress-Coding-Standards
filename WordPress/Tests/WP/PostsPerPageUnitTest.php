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
 * Unit test class for the PostsPerPage sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  This sniff has been split into two, with the check for high pagination
 *                 limit being part of the WP category, and the check for pagination
 *                 disabling being part of the VIP category.
 */
class PostsPerPageUnitTest extends AbstractSniffUnitTest {

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
			4  => 1,
			10 => 1,
			11 => 1,
			13 => 1,
			22 => 1,
			23 => 1,
			24 => 1,
			29 => 1,
		);
	}

}
