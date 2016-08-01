<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package  PHP\CodeSniffer\WordPress-Coding-Standards
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Unit test class for the ValidatedSanitizedInputSniff
 *
 * @package   PHP\CodeSniffer\WordPress-Coding-Standards
 * @author    Shady Sharaf <shady@x-team.com>
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class WordPress_Tests_VIP_ValidatedSanitizedInputUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			5 => 3,
			7 => 1,
			10 => 1,
			20 => 1,
			33 => 3,
			65 => 1,
			79 => 1,
			80 => 1,
			81 => 1,
			82 => 1,
			85 => 1,
			90 => 1,
			93 => 1,
			96 => 1,
			100 => 2,
			101 => 1,
			104 => 2,
			105 => 1,
			114 => 2,
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
