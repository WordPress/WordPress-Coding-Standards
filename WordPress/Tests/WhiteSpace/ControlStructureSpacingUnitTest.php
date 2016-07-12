<?php
/**
 * Unit test class for the ControlStructureSpacing sniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Akeda Bagus <akeda@x-team.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Unit test class for the ControlStructureSpacing sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Akeda Bagus <akeda@x-team.com>
 * @author   Greg Sherwood <gsherwood@squiz.net>
 * @author   Marc McIntyre <mmcintyre@squiz.net>
 * @license  https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class WordPress_Tests_WhiteSpace_ControlStructureSpacingUnitTest extends AbstractSniffUnitTest {

	/**
	 * Skip this test on PHP 5.2.
	 *
	 * @since 0.9.0
	 *
	 * @return bool Whether to skip this test.
	 */
	protected function shouldSkipTest() {
		return version_compare( PHP_VERSION, '5.3.0', '<' );
	}


	/**
	 * Returns the lines where errors should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of errors that should occur on that line.
	 *
	 * @return array(int => int)
	 */
	public function getErrorList() {
		$ret = array(
			4  => 2,
			17 => 2,
			29 => 5,
			37 => 1,
			41 => 1,
			42 => 1,
			49 => 5,
			58 => 3,
			67 => 1,
			68 => 1,
			69 => 1,
			71 => 1,
			72 => 1,
			81 => 3,
			82 => 1,
			85 => 1,
			91 => 2,
			92 => 1,
			94 => 1,
			95 => 1,
			97 => 1,
			98 => 1,
		);

		// Uncomment when "$blank_line_check" parameter will be "true" by default.
		/*
		$ret[29] += 1;
		$ret[33]  = 1;
		$ret[36]  = 1;
		$ret[38]  = 1;
		 */

		return $ret;

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
