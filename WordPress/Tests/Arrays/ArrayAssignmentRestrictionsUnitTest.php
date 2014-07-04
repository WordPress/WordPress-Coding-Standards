<?php
/**
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Shady Sharaf <shady@x-team.com>
 */
class WordPress_Tests_Arrays_ArrayAssignmentRestrictionsUnitTest extends AbstractSniffUnitTest
{

	protected function setUp() {
		parent::setUp();

		WordPress_Sniffs_Arrays_ArrayAssignmentRestrictionsSniff::$groups = array(
			'posts_per_page' => array(
				'type' => 'error',
				'message' => 'Found assignment value of %s to be %s',
				'keys' => array(
					'foo',
					'bar',
					),
				),
			);
	}

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
					3 => 1,
					5 => 1,
					7 => 2,
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
						);

		}//end getWarningList()


}//end class
