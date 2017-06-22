<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the WP_DeprecatedFunctions sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0
 */
class WordPress_Tests_WP_DeprecatedFunctionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {

		$errors = array_fill( 8, 268, 1 );

		// Unset the lines related to version comments.
		unset(
			$errors[10],  $errors[12],  $errors[14],  $errors[16],  $errors[28],
			$errors[54],  $errors[56],  $errors[58],  $errors[71],  $errors[74],
			$errors[78],  $errors[115], $errors[119], $errors[141], $errors[152],
			$errors[160], $errors[187], $errors[203], $errors[217], $errors[221],
			$errors[228], $errors[240], $errors[247], $errors[251], $errors[256],
			$errors[261], $errors[269]
		);

		return $errors;
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {

		$warnings = array_fill( 281, 19, 1 );

		// Unset the lines related to version comments.
		unset(
			$warnings[288], $warnings[291], $warnings[298]
		);

		return $warnings;
	}

} // End class.
