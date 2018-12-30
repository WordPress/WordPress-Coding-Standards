<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the StrictComparisons sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.4.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class StrictComparisonsUnitTest extends AbstractSniffUnitTest {

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
			3  => 1,
			10 => 1,
			12 => 1,
			19 => 1, // Whitelist comment deprecation warning.
			24 => 1,
			29 => 1,
		);
	}

}
