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
use WordPressCS\WordPress\PHPCSHelper;

/**
 * Unit test class for the TypeCasts sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since 1.2.0
 */
class TypeCastsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			12 => 1,
			13 => 1,
			14 => 1,
			15 => 1,
			32 => 1,
			33 => 1,
			34 => 1,
			35 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			18 => 1,
			19 => 1,
			20 => ( version_compare( PHPCSHelper::get_version(), '3.4.0', '<' ) === true ? 0 : 1 ),
			21 => 1,
			36 => 1,
			37 => 1,
		);
	}
}
