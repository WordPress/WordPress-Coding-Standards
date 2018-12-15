<?php
/**
 * PHPCS cross-version compatibility helper.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 * @since   0.13.0
 */

if ( ! \defined( 'WPCS_PHPCS_ALIASES_SET' ) ) {

	define( 'WPCS_PHPCS_ALIASES_SET', true );

	/*
	 * Register our own autoloader for the WPCS abstract classes & the helper class.
	 *
	 * This can be removed once the minimum required version of WPCS for the
	 * PHPCS 3.x branch has gone up to 3.1.0 (unreleased as of yet) or
	 * whichever version contains the fix for upstream #1591.
	 *
	 * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/1564
	 * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/1591
	 */
	spl_autoload_register(
		function ( $class ) {
			// Only try & load our own classes.
			if ( stripos( $class, 'WordPressCS' ) !== 0 ) {
				return;
			}

			$class = str_replace( 'WordPressCS\\', '', $class );

			// PHPCS handles the Test and Sniff classes without problem.
			if ( stripos( $class, '\Tests\\' ) !== false || stripos( $class, '\Sniffs\\' ) !== false ) {
				return;
			}

			$file = dirname( __DIR__ ) . DIRECTORY_SEPARATOR . strtr( $class, '\\', DIRECTORY_SEPARATOR ) . '.php';

			if ( file_exists( $file ) ) {
				include_once $file;
			}
		}
	);
}
