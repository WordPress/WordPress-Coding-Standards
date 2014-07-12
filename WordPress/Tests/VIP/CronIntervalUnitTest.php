<?php
/**
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Shady Sharaf <shady@x-team.com>
 */
class WordPress_Tests_VIP_CronIntervalUnitTest extends AbstractSniffUnitTest
{

		/**
		 * Returns the lines where errors should occur.
		 *
		 * The key of the array should represent the line number and the value
		 * should represent the number of errors that should occur on that line.
		 *
		 * @return array(int => int)
		 */
		public function getErrorList()
		{
			return array(
				10 => 1,
				15 => 1,
				35 => 1,
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
		public function getWarningList()
		{
			return array(
				37 => 1,
					);

		}//end getWarningList()


}//end class
