<?php
/**
 * Unit test class for the EscapeOutput sniff.
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
 * Unit test class for the EscapeOutput sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Akeda Bagus <akeda@x-team.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class WordPress_Tests_XSS_EscapeOutputUnitTest extends AbstractSniffUnitTest {

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
			17 => 1,
			19 => 1,
			36 => 1,
			39 => 1,
			40 => 1,
			41 => 1,
			43 => 1,
			46 => 1,
			53 => 1,
			59 => 1,
			60 => 1,
			65 => 1,
			68 => 1,
			71 => 1,
			73 => 1,
			75 => 1,
			101 => 1,
			103 => 1,
			111 => 1,
			112 => 1,
			113 => 1,
			114 => 1,
			125 => 1,
			131 => 1,
			135 => 1,
			138 => 1,
			145 => 1,
			147 => 1,
			149 => 1,
			152 => 2,
			159 => 1,
			162 => 1,
			169 => 1,
			172 => 1,
			173 => 1,
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
