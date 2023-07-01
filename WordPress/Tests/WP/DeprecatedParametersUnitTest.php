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
 * Unit test class for the DeprecatedParameters sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.12.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * @covers \WordPressCS\WordPress\Sniffs\WP\DeprecatedParametersSniff
 */
final class DeprecatedParametersUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		$start_line = 42;
		$end_line   = 95;
		$errors     = array_fill( $start_line, ( ( $end_line - $start_line ) + 1 ), 1 );

		$errors[22] = 1;
		$errors[23] = 1;
		$errors[24] = 1;

		// Named param.
		$errors[38] = 1;

		// Override number of errors.
		$errors[49] = 2;
		$errors[75] = 2;

		return $errors;
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		$start_line = 98;
		$end_line   = 99;
		$errors     = array_fill( $start_line, ( ( $end_line - $start_line ) + 1 ), 1 );

		return $errors;
	}
}
