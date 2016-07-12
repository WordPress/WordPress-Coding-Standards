<?php

/**
 * Unit test class for the NonceVerification Sniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    J.D. Grimes <jdg@codesymphony.co>
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

class WordPress_Tests_CSRF_NonceVerificationUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of errors that should occur on that line.
	 *
	 * @return array(int => int)
	 */
	public function getErrorList() {

		return array(
			5 => 1,
			9 => 1,
			31 => 1,
			44 => 1,
			48 => 1,
			69 => 1,
			89 => 1,
			113 => 1,
			114 => 1,
		);
	} // end getErrorList()


	/**
	 * Returns the lines where warnings should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of warnings that should occur on that line.
	 *
	 * @return array(int => int)
	 */
	public function getWarningList() {
		return array();
	} // end getWarningList()

} // end class
