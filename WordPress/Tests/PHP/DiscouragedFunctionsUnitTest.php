<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package  PHP\CodeSniffer\WordPress-Coding-Standards
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Unit test class for the DiscouragedFunctions sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @package   PHP\CodeSniffer\WordPress-Coding-Standards
 * @author    Akeda Bagus <akeda@x-team.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class WordPress_Tests_PHP_DiscouragedFunctionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of errors that should occur on that line.
	 *
	 * @return array(int => int)
	 */
	public function getErrorList() {
		return array();

	}

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
			8 => 1,
			9 => 1,
			15 => 1,
			17 => 1,
			19 => 1,
			21 => 1,
			23 => 1,
			25 => 1,
			27 => 1,
			29 => 1,
			31 => 1,
			33 => 1,
			35 => 1,
			37 => 1,
			39 => 1,
			45 => 1,
			47 => 1,
			52 => 1,
			54 => 1,
		);

	}

} // End class.
