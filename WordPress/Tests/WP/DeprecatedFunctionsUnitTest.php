<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\WP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the WP_DeprecatedFunctions sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class DeprecatedFunctionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {

		$errors = array_fill( 8, 310, 1 );

		// Unset the lines related to version comments.
		unset(
			$errors[10], $errors[12], $errors[14], $errors[16], $errors[29],
			$errors[55], $errors[57], $errors[59], $errors[73], $errors[76],
			$errors[80], $errors[118], $errors[125], $errors[161], $errors[174],
			$errors[178], $errors[210], $errors[233], $errors[251], $errors[255],
			$errors[262], $errors[274], $errors[281], $errors[285], $errors[290],
			$errors[295], $errors[303], $errors[310]
		);

		return $errors;
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {

		$warnings = array_fill( 323, 17, 1 );

		// Unset the lines related to version comments.
		unset(
			$warnings[326], $warnings[333], $warnings[335]
		);

		return $warnings;
	}

} // End class.
