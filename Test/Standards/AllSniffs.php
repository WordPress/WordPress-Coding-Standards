<?php
/**
 * A test class for testing all sniffs for installed standards.
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

use PHP_CodeSniffer_Standards_AllSniffs;
use PHP_CodeSniffer;
use PHPUnit_Framework_TestSuite;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
/* End of WPCS adjustment */

/**
 * A test class for testing all sniffs for installed standards.
 *
 * Usage: phpunit AllSniffs.php
 *
 * This test class loads all unit tests for all installed standards into a
 * single test suite and runs them. Errors are reported on the command line.
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
class AllSniffs extends PHP_CodeSniffer_Standards_AllSniffs
{

    /**
     * Add all sniff unit tests into a test suite.
     *
     * Sniff unit tests are found by recursing through the 'Tests' directory
     * of each installed coding standard.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHP CodeSniffer Standards');

        /* Start of WPCS adjustment */
        // Set the correct path to PHPCS.
        $isInstalled = !is_file(\PHPCS_DIR.'/CodeSniffer.php');
        /* End of WPCS adjustment */

        // Optionally allow for ignoring the tests for one or more standards.
        $ignoreTestsForStandards = getenv('PHPCS_IGNORE_TESTS');
        if ($ignoreTestsForStandards === false) {
            $ignoreTestsForStandards = array();
        } else {
            $ignoreTestsForStandards = explode(',', $ignoreTestsForStandards);
        }

        $installedPaths = PHP_CodeSniffer::getInstalledStandardPaths();
        foreach ($installedPaths as $path) {
            $path      = realpath($path);
            $origPath  = $path;
            $standards = PHP_CodeSniffer::getInstalledStandards(true, $path);

            // If the test is running PEAR installed, the built-in standards
            // are split into different directories; one for the sniffs and
            // a different file system location for tests.
            if ($isInstalled === true
                && is_dir($path.\DIRECTORY_SEPARATOR.'Generic') === true
            ) {
                $path = dirname(__FILE__);
            }

            foreach ($standards as $standard) {
                if (in_array($standard, $ignoreTestsForStandards, true)) {
                    continue;
                }

                $testsDir = $path.\DIRECTORY_SEPARATOR.$standard.\DIRECTORY_SEPARATOR.'Tests'.\DIRECTORY_SEPARATOR;

                if (is_dir($testsDir) === false) {
                    // No tests for this standard.
                    continue;
                }

                $di = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($testsDir));

                foreach ($di as $file) {
                    // Skip hidden files.
                    if (substr($file->getFilename(), 0, 1) === '.') {
                        continue;
                    }

                    // Tests must have the extension 'php'.
                    $parts = explode('.', $file);
                    $ext   = array_pop($parts);
                    if ($ext !== 'php') {
                        continue;
                    }

                    $filePath  = $file->getPathname();
                    $className = str_replace($path.\DIRECTORY_SEPARATOR, '', $filePath);
                    $className = substr($className, 0, -4);
                    $className = str_replace(\DIRECTORY_SEPARATOR, '_', $className);

                    // Include the sniff here so tests can use it in their setup() methods.
                    $parts = explode('_', $className);
                    if (isset($parts[0],$parts[2],$parts[3]) === true) {
                        $sniffPath = $origPath.\DIRECTORY_SEPARATOR.$parts[0].\DIRECTORY_SEPARATOR.'Sniffs'.\DIRECTORY_SEPARATOR.$parts[2].\DIRECTORY_SEPARATOR.$parts[3];
                        $sniffPath = substr($sniffPath, 0, -8).'Sniff.php';

                        if (file_exists($sniffPath) === true) {
                            include_once $sniffPath;
                            include_once $filePath;

                            /* Start of WPCS adjustment */
                            // Support the use of PHP namespaces. If the class name we included
                            // contains namespace separators instead of underscores, use this as the
                            // class name from now on.
                            $classNameNS = str_replace('_', '\\', $className);
                            if (class_exists($classNameNS, false) === true) {
                                $className = $classNameNS;
                            }
                            /* End of WPCS adjustment */

                            $GLOBALS['PHP_CODESNIFFER_STANDARD_DIRS'][$className] = $path;
                            $suite->addTestSuite($className);
                        } else {
                            self::$orphanedTests[] = $filePath;
                        }
                    } else {
                        self::$orphanedTests[] = $filePath;
                    }
                }//end foreach
            }//end foreach
        }//end foreach

        return $suite;

    }//end suite()


}//end class
