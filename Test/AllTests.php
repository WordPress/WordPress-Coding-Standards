<?php
/**
 * A test class for running all PHP_CodeSniffer unit tests.
 *
 * PHP version 5
 *
 * {@internal WPCS: File copied from PHPCS 2.x to overload one method.}}
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/* Start of WPCS adjustment */
namespace WordPressCS\Test;

use WordPressCS\Test\AllSniffs;
use PHP_CodeSniffer_AllTests;
use PHP_CodeSniffer_TestSuite;
/* End of WPCS adjustment */

/**
 * A test class for running all PHP_CodeSniffer unit tests.
 *
 * Usage: phpunit AllTests.php
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class AllTests extends PHP_CodeSniffer_AllTests {

    /**
     * Add all PHP_CodeSniffer test suites into a single test suite.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $GLOBALS['PHP_CODESNIFFER_STANDARD_DIRS'] = array();

        // Use a special PHP_CodeSniffer test suite so that we can
        // unset our autoload function after the run.
        $suite = new PHP_CodeSniffer_TestSuite('PHP CodeSniffer');

        /* Start of WPCS adjustment */
        // We need to point to the WPCS version of the referenced class
        // and we may as well bypass the loading of the PHPCS core unit tests
        // while we're at it too.
        $suite->addTest(AllSniffs::suite());
        /* End of WPCS adjustment */

        // Unregister this here because the PEAR tester loads
        // all package suites before running then, so our autoloader
        // will cause problems for the packages included after us.
        spl_autoload_unregister(array('PHP_CodeSniffer', 'autoload'));

        return $suite;

    }//end suite()


}//end class
