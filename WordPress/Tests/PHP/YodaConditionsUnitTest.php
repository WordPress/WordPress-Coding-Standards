<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package  PHP\CodeSniffer\WordPress-Coding-Standards
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Unit test class for the YodaConditions sniff.
 *
 * @package PHP\CodeSniffer\WordPress-Coding-Standards
 * @author  Matt Robinson
 * @since   2014-12-11
 */
class WordPress_Tests_PHP_YodaConditionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			2 => 2,
			4 => 2,
			11 => 1,
			18 => 1,
			25 => 1,
			32 => 1,
			49 => 1,
			55 => 1,
			62 => 1,
			68 => 1,
			84 => 1,
			88 => 1,
			90 => 1,
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
