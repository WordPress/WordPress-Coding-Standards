<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package PHP\CodeSniffer\WordPress-Coding-Standards
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Unit test class for the POSIXFunctions sniff.
 *
 * @package PHP\CodeSniffer\WordPress-Coding-Standards
 * @author  Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 * @since   0.10.0
 */
class WordPress_Tests_PHP_POSIXFunctionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			13 => 1,
			16 => 1,
			18 => 1,
			20 => 1,
			22 => 1,
			24 => 1,
			26 => 1,
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

} // End class.
