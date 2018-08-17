<?php
/**
 * PHPCS cross-version compatibility helper.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 * @since   0.13.0
 */

/*
 * Alias a number of PHPCS 3.x classes to their PHPCS 2.x equivalents.
 *
 * This file is auto-loaded by PHPCS 3.x before any sniffs are loaded
 * through the PHPCS 3.x `<autoload>` ruleset directive.
 *
 * {@internal The PHPCS files have been reorganized in PHPCS 3.x, quite
 * a few "old" classes have been split and spread out over several "new"
 * classes. In other words, this will only work for a limited number
 * of classes.}}
 *
 * {@internal The `class_exists` wrappers are needed to play nice with other
 * external PHPCS standards creating cross-version compatibility in the same
 * manner.}}
 */
if ( ! \defined( 'WPCS_PHPCS_ALIASES_SET' ) ) {
	// PHPCS base classes/interface.
	if ( ! interface_exists( '\PHP_CodeSniffer_Sniff' ) ) {
		class_alias( 'PHP_CodeSniffer\Sniffs\Sniff', '\PHP_CodeSniffer_Sniff' );
	}
	if ( ! class_exists( '\PHP_CodeSniffer_File' ) ) {
		class_alias( 'PHP_CodeSniffer\Files\File', '\PHP_CodeSniffer_File' );
	}
	if ( ! class_exists( '\PHP_CodeSniffer_Tokens' ) ) {
		class_alias( 'PHP_CodeSniffer\Util\Tokens', '\PHP_CodeSniffer_Tokens' );
	}

	// PHPCS classes which are being extended by WPCS sniffs.
	if ( ! class_exists( '\PHP_CodeSniffer_Standards_AbstractVariableSniff' ) ) {
		class_alias( 'PHP_CodeSniffer\Sniffs\AbstractVariableSniff', '\PHP_CodeSniffer_Standards_AbstractVariableSniff' );
	}
	if ( ! class_exists( '\PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff' ) ) {
		class_alias( 'PHP_CodeSniffer\Standards\PEAR\Sniffs\NamingConventions\ValidFunctionNameSniff', '\PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff' );
	}
	if ( ! class_exists( '\Squiz_Sniffs_WhiteSpace_OperatorSpacingSniff' ) ) {
		class_alias( 'PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\OperatorSpacingSniff', '\Squiz_Sniffs_WhiteSpace_OperatorSpacingSniff' );
	}
	if ( ! class_exists( '\Squiz_Sniffs_WhiteSpace_SemicolonSpacingSniff' ) ) {
		class_alias( 'PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SemicolonSpacingSniff', '\Squiz_Sniffs_WhiteSpace_SemicolonSpacingSniff' );
	}

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
		}
	);
}
