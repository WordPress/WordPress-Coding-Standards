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
 * Unit test class for the WP_DeprecatedClasses sniff.
 *
 * @since 0.12.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 *
 * @covers \WordPressCS\WordPress\Sniffs\WP\DeprecatedClassesSniff
 */
final class DeprecatedClassesUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList() {
		$start_line = 9;
		$end_line   = 28;
		$errors     = array_fill( $start_line, ( ( $end_line - $start_line ) + 1 ), 1 );

		// Unset the lines related to version comments.
		unset( $errors[16], $errors[18], $errors[21], $errors[26] );

		return $errors;
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList() {
		return array();
	}
}
