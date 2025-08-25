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
 * @since 0.11.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 *
 * @covers \WordPressCS\WordPress\Sniffs\WP\DeprecatedFunctionsSniff
 */
final class DeprecatedFunctionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The test file to check for errors.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'DeprecatedFunctionsUnitTest.1.inc':
				$start_line = 8;
				$end_line   = 420;
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
					$errors[353],
					$errors[357],
					$errors[359],
					$errors[361],
					$errors[363],
					$errors[369],
					$errors[371],
					$errors[373],
					$errors[383],
					$errors[386],
					$errors[410]
				);

				return $errors;

			case 'DeprecatedFunctionsUnitTest.2.inc':
				return array(
					6 => 1,
				);

			default:
				return array();
		}
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @param string $testFile The test file to check for warnings.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'DeprecatedFunctionsUnitTest.1.inc':
				$start_line = 426;
				$end_line   = 443;
				$warnings   = array_fill( $start_line, ( ( $end_line - $start_line ) + 1 ), 1 );

				// Unset the lines related to version comments.
				unset(
					$warnings[429],
					$warnings[432],
					$warnings[442]
				);

				return $warnings;

			default:
				return array();
		}
	}
}
