<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Unit test class for the DisallowAlternativeOpenTag sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 */
class WordPress_Tests_PHP_DisallowAlternativeOpenTagUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of errors that should occur on that line.
	 *
	 * @return array<int, int>
	 */
	public function getErrorList() {
		$asp_enabled   = (boolean) ini_get( 'asp_tags' );
		$short_enabled = (boolean) ini_get( 'short_open_tag' );

		$errors = array(
			6 => 1,
		);

		if ( true === $asp_enabled ) {
			$errors[4] = 1;
		}
		if ( true === $asp_enabled && ( true === $short_enabled || defined( 'HHVM_VERSION' ) === true ) ) {
			$errors[5] = 1;
		}

		return $errors;
	} // end getErrorList()

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of warnings that should occur on that line.
	 *
	 * @return array<int, int>
	 */
	public function getWarningList() {
		$asp_enabled   = (boolean) ini_get( 'asp_tags' );
		$short_enabled = (boolean) ini_get( 'short_open_tag' );

		$warnings = array();

		if ( false === $asp_enabled ) {
			$warnings = array(
				4 => 1,
				5 => 1,
			);
		} elseif ( false === $short_enabled && false === defined( 'HHVM_VERSION' ) ) {
			$warnings = array(
				5 => 1,
			);
		}

		return $warnings;

	} // end getWarningList()

} // end class
