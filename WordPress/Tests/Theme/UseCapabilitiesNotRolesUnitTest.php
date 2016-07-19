<?php
/**
 * WordPress Coding Standard
 * UseCapabilitiesNotRoles Unit Test.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Unit test class for the UseCapabilitiesNotRoles sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    khacoder
 */
class WordPress_Tests_Theme_UseCapabilitiesNotRolesUnitTest extends AbstractSniffUnitTest
{
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
			6 => 1,
			7 => 1,
			8 => 1,
			9 => 1,
			10 => 1,
			11 => 1,
			12 => 1,
			13 => 1,
			14 => 1,
			15 => 1,
			24 => 1,
			36 => 1,
			39 => 1,
		);
	}//end getErrorList()

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of warnings that should occur on that line.
	 *
	 * @return array(int => int)
	 */
	public function getWarningList() {
		return array(
			16 => 1,
			18 => 1,
			21 => 1,
			30 => 1,
			34 => 1,
		);
	}//end getWarningList()
}//end class
