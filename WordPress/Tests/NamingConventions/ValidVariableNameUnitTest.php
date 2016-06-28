<?php
/**
 * Unit test class for WordPress_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @author    Weston Ruter
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Unit test class for WordPress_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @author    Weston Ruter
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class WordPress_Tests_NamingConventions_ValidVariableNameUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of errors that should occur on that line.
	 *
	 * @return array<int, int>
	 */
	public function getErrorList() {
		$errors = array(
			2   => 1,
			5   => 1,
			8   => 1,
			11   => 1,
			13   => 1,
			16   => 1,
			18   => 1,
			21   => 1,
			23   => 1,
			26   => 1,
			29   => 1,
			32   => 1,
			34   => 1,
			38   => 1,
			40   => 1,
			43   => 1,
			51   => 1,
			54   => 1,
			55   => 1,
			57   => 1,
			60   => 1,
			61   => 1,
			64   => 1,
			65   => 1,
			70   => 1,
			77   => 1,
			78   => 1,
			79   => 1,
			80   => 1,
			82   => 1,
			86   => 1,
			87   => 1,
			88   => 1,
			99   => 1,
			100  => 1,
			101  => 1,
			102  => 1,
			103  => 1,
			104  => 1,
			105  => 1,
			121  => 1,
		);

		return $errors;

	}//end getErrorList()

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of warnings that should occur on that line.
	 *
	 * @return array<int, int>
	 */
	public function getWarningList() {
		return array();
	}
}//end class
