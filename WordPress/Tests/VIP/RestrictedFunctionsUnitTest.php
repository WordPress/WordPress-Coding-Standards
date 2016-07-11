<?php
/**
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
class WordPress_Tests_VIP_RestrictedFunctionsUnitTest extends AbstractSniffUnitTest
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
            3  => 1,
            5  => 1,
            21 => 1,
            34 => version_compare( PHP_VERSION, '5.3.0', '>=' ) ? 0 : 1,
            36 => 1,
            38 => 1,
            40 => 1,
            42 => 1,
            44 => 1,
            46 => 1,
            48 => 1,
            50 => 1,
            53 => 1,
            54 => 1,
            55 => 1,
            56 => 1,
            57 => 1,
            62 => 1,
            63 => 1,
            64 => 1,
            65 => 1,
            66 => 1,
            67 => 1,
            68 => 1,
            69 => 1,
            70 => 1,
            71 => 1,
            74 => 1,
            75 => 2,
            76 => 1,
            77 => 1,
            78 => 1,
            79 => 1,
            80 => 1,
            81 => 1,
            82 => 1,
            83 => 1,
            84 => 1,
            85 => 1,
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
            7  => 1,
            9  => 1,
            11 => 1,
            13 => 1,
            15 => 1,
            17 => 1,
            19 => 1,
            58 => 1,
            59 => 1,
            61 => 1,
            72 => 1,
            );

    }//end getWarningList()


}//end class
