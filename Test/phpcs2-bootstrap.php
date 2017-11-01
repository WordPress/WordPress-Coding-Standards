<?php
/**
 * Bootstrap file for running the tests on PHPCS 2.x.
 *
 * Load the PHPCS test classes and the WPCS ones.
 *
 * {@internal The PHPCS 2.x test classes do not allow for namespaced unit
 * test classes. Some small changes to the classes change that which
 * is why we'll be using our own versions of some files.}}
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 * @since   0.13.0
 */

$ds = DIRECTORY_SEPARATOR;

// Get the PHPCS dir from an environment variable.
$phpcsDir = getenv( 'PHPCS_DIR' );

if ( false === $phpcsDir && is_dir( dirname( __DIR__ ) . $ds . 'vendor' . $ds . 'squizlabs' . $ds . 'php_codesniffer' ) ) {
	$phpcsDir = dirname( __DIR__ ) . $ds . 'vendor' . $ds . 'squizlabs' . $ds . 'php_codesniffer';
} elseif ( false !== $phpcsDir ) {
	$phpcsDir = realpath( $phpcsDir );
}

if ( false === $phpcsDir || ! is_dir( $phpcsDir . $ds . 'CodeSniffer' )
	|| ! file_exists( $phpcsDir . $ds . 'tests' . $ds . 'AllTests.php' )
) {
	echo 'Uh oh... can\'t find PHPCS. Are you sure you are using PHPCS 2.x ?

If you use Composer, please run `composer install`.
Otherwise, make sure you set a `PHPCS_DIR` environment variable in your phpunit.xml file
pointing to the PHPCS directory.

Please read the contributors guidelines for more information:
https://is.gd/contributing2WPCS
';

	die( 1 );
} else {
	define( 'PHPCS_DIR', $phpcsDir );
}

// Load the PHPCS test classes and the WPCS versions where necessary.
require_once PHPCS_DIR . $ds . 'tests' . $ds . 'AllTests.php';
require_once __DIR__ . $ds . 'Standards' . $ds . 'AllSniffs.php';
require_once __DIR__ . $ds . 'Standards' . $ds . 'AbstractSniffUnitTest.php';

class_alias( 'WordPressCS\Test\AbstractSniffUnitTest', 'PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest' );

unset( $ds, $phpcsDir );
