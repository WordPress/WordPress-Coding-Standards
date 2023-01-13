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
 * Unit test class for the WP_DeprecatedFunctions sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
final class DeprecatedFunctionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		$start_line = 8;
		$end_line   = 356;
		$errors     = array_fill( $start_line, ( ( $end_line - $start_line ) + 1 ), 1 );

		// Unset the lines related to version comments.
		unset(
			$errors[10],
			$errors[12],
			$errors[14],
			$errors[16],
			$errors[29],
			$errors[55],
			$errors[57],
			$errors[59],
			$errors[73],
			$errors[76],
			$errors[80],
			$errors[102], // Undeprecated function.
			$errors[118],
			$errors[125],
			$errors[162],
			$errors[175],
			$errors[179],
			$errors[211],
			$errors[234],
			$errors[252],
			$errors[256],
			$errors[263],
			$errors[275],
			$errors[282],
			$errors[286],
			$errors[291],
			$errors[296],
			$errors[304],
			$errors[311],
			$errors[319],
			$errors[323],
			$errors[330],
			$errors[332],
			$errors[337],
			$errors[340],
			$errors[344],
			$errors[346],
			$errors[353]
		);

		return $errors;
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		$start_line = 362;
		$end_line   = 386;
		$warnings   = array_fill( $start_line, ( ( $end_line - $start_line ) + 1 ), 1 );

		// Unset the lines related to version comments.
		unset(
			$warnings[363],
			$warnings[365],
			$warnings[367],
			$warnings[373],
			$warnings[375],
			$warnings[377]
		);

		// Temporarily until PHPCS supports PHP 8.2.
		if ( \PHP_VERSION_ID >= 80200 ) {
			unset( $warnings[364] ); // Function call to readonly.
		}

		return $warnings;
	}

}
