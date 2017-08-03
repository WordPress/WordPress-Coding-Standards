<?php
/**
 * Bootstrap file for running the tests on PHPCS 3.x.
 *
 * Load the PHPCS autoloader and the WPCS PHPCS cross-version helpers.
 *
 * {@internal We need to load the PHPCS autoloader first, so as to allow their
 * auto-loader to find the classes we want to alias for PHPCS 3.x.
 * This aliasing has to be done before any of the test classes are loaded by the
 * PHPCS native unit test suite to prevent fatal errors.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 * @since   0.13.0
 */

if ( ! defined( 'PHP_CODESNIFFER_IN_TESTS' ) ) {
	define( 'PHP_CODESNIFFER_IN_TESTS', true );
}

$ds = DIRECTORY_SEPARATOR;

// Get the PHPCS dir from an environment variable.
$phpcsDir = getenv( 'PHPCS_DIR' );

// This may be a Composer install.
if ( false === $phpcsDir && is_dir( dirname( __DIR__ ) . $ds . 'vendor' . $ds . 'squizlabs' . $ds . 'php_codesniffer' ) ) {
	$phpcsDir  = dirname( __DIR__ ) . $ds . 'vendor' . $ds . 'squizlabs' . $ds . 'php_codesniffer';
} elseif ( false !== $phpcsDir ) {
	$phpcsDir = realpath( $phpcsDir );
}

// Try and load the PHPCS autoloader.
if ( false !== $phpcsDir && file_exists( $phpcsDir . $ds . 'autoload.php' ) ) {
	require_once $phpcsDir . $ds . 'autoload.php';
} else {
	echo 'Uh oh... can\'t find PHPCS. Are you sure you are using PHPCS 3.x ?

If you use Composer, please run `composer install`.
Otherwise, make sure you set a `PHPCS_DIR` environment variable in your phpunit.xml file
pointing to the PHPCS directory.

Please read the contributors guidelines for more information:
https://is.gd/contributing2WPCS
';

	die( 1 );
}

// Load our class aliases.
include_once dirname( __DIR__ ) . $ds . 'WordPress' . $ds . 'PHPCSAliases.php';
unset( $ds, $phpcsDir );

/*
 * Register our own autoloader for the WPCS abstract classes & the helper class.
 *
 * This can be removed once the minimum required version of WPCS for the
 * PHPCS 3.x branch has gone up to 3.1.0 (unreleased as of yet).
 *
 * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/1564
 */
spl_autoload_register( function ( $class ) {
	// Only try & load our own classes.
	if ( stripos( $class, 'WordPress' ) !== 0 ) {
		return;
	}

	// PHPCS handles the Test and Sniff classes without problem.
	if ( stripos( $class, '\Tests\\' ) !== false || stripos( $class, '\Sniffs\\' ) !== false ) {
		return;
	}

	$file = dirname( __DIR__ ) . DIRECTORY_SEPARATOR . strtr( $class, '\\', DIRECTORY_SEPARATOR ) . '.php';

	if ( file_exists( $file ) ) {
		include_once $file;
	}
} );
