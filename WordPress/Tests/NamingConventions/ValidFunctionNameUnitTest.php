<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\NamingConventions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ValidFunctionName sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   2013-06-11
 * @since   0.13.0     Class name changed: this class is now namespaced.
 */
class ValidFunctionNameUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			3   => 1,
			9   => 1,
			13  => 1,
			15  => 1,
			79  => 2,
			80  => 2,
			81  => 2,
			82  => 2,
			83  => 2,
			84  => 2,
			85  => 2,
			86  => 2,
			87  => 2,
			88  => 2,
			89  => 2,
			106 => 2,
			116 => 1,
			117 => 1,
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
