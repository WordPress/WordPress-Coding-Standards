<?php
/**
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Shady Sharaf <shady@x-team.com>
 */
class WordPress_Tests_Variables_VariableRestrictionsUnitTest extends AbstractSniffUnitTest
{

	protected function setUp() {
		parent::setUp();

		WordPress_Sniffs_Variables_VariableRestrictionsSniff::$groups = array(
			'test' => array(
				'type' => 'error',
				'message' => 'Detected usage of %s',
				'object_vars' => array(
					'$foo->bar',
					'FOO::var',
					'FOO::reg*',
					'FOO::$static',
					),
				'array_members' => array(
					'$foo[\'test\']',
					),
				'variables' => array(
					'$taz',
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
               11 => 1,
               15 => 1,
               17 => 1,
               21 => 1,
               23 => 1,
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
