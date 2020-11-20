<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\Namespaces;
use WordPressCS\WordPress\Sniff as WPCS_Sniff;

/**
 * Helper utilities for sniffs which need to take into account whether the
 * code under examination is unit test code or not.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   3.0.0 The properties and method in this trait were previously contained in the
 *                `WordPressCS\WordPress\Sniff` class and have been moved here.
 */
trait IsUnitTestTrait {

	/**
	 * Custom list of classes which test classes can extend.
	 *
	 * This property allows end-users to add to the $test_class_whitelist via their ruleset.
	 * This property will need to be set for each sniff which uses the
	 * `is_test_class()` method.
	 * Currently the method is used by the `WordPress.WP.GlobalVariablesOverride`,
	 * `WordPress.NamingConventions.PrefixAllGlobals` and the `WordPress.Files.Filename` sniffs.
	 *
	 * Example usage:
	 * <rule ref="WordPress.[Subset].[Sniffname]">
	 *  <properties>
	 *   <property name="custom_test_class_whitelist" type="array">
	 *     <element value="My_Plugin_First_Test_Class"/>
	 *     <element value="My_Plugin_Second_Test_Class"/>
	 *   </property>
	 *  </properties>
	 * </rule>
	 *
	 * @since 0.11.0
	 * @since 3.0.0  Moved from the Sniff class to this dedicated Trait.
	 *
	 * @var string|string[]
	 */
	public $custom_test_class_whitelist = array();

	/**
	 * Whitelist of classes which test classes can extend.
	 *
	 * @since 0.11.0
	 * @since 3.0.0  Moved from the Sniff class to this dedicated Trait.
	 *
	 * @var string[]
	 */
	protected $test_class_whitelist = array(
		'WP_UnitTestCase_Base'                       => true,
		'WP_UnitTestCase'                            => true,
		'WP_Ajax_UnitTestCase'                       => true,
		'WP_Canonical_UnitTestCase'                  => true,
		'WP_Test_REST_TestCase'                      => true,
		'WP_Test_REST_Controller_Testcase'           => true,
		'WP_Test_REST_Post_Type_Controller_Testcase' => true,
		'WP_XMLRPC_UnitTestCase'                     => true,
		'PHPUnit_Framework_TestCase'                 => true,
		'PHPUnit\Framework\TestCase'                 => true,
		// PHPUnit native TestCase class when imported via use statement.
		'TestCase'                                   => true,
	);

	/**
	 * Check if a class token is part of a unit test suite.
	 *
	 * Unit test classes are identified as such:
	 * - Class which either extends WP_UnitTestCase or PHPUnit_Framework_TestCase
	 *   or a custom whitelisted unit test class.
	 *
	 * @since 0.12.0 Split off from the `is_token_in_test_method()` method.
	 * @since 1.0.0  Improved recognition of namespaced class names.
	 * @since 3.0.0  Moved from the Sniff class to this dedicated Trait.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the token to be examined.
	 *                                               This should be a class, anonymous class or trait token.
	 *
	 * @return bool True if the class is a unit test class, false otherwise.
	 */
	protected function is_test_class( File $phpcsFile, $stackPtr ) {

		$tokens = $phpcsFile->getTokens();

		if ( isset( $tokens[ $stackPtr ], Tokens::$ooScopeTokens[ $tokens[ $stackPtr ]['code'] ] ) === false ) {
			return false;
		}

		// Add any potentially whitelisted custom test classes to the whitelist.
		$whitelist = WPCS_Sniff::merge_custom_array(
			$this->custom_test_class_whitelist,
			$this->test_class_whitelist
		);

		/*
		 * Show some tolerance for user input.
		 * The custom test class names should be passed as FQN without a prefixing `\`.
		 */
		foreach ( $whitelist as $k => $v ) {
			$whitelist[ $k ] = ltrim( $v, '\\' );
		}

		// Is the class/trait one of the whitelisted test classes ?
		$namespace = Namespaces::determineNamespace( $phpcsFile, $stackPtr );
		$className = $phpcsFile->getDeclarationName( $stackPtr );
		if ( '' !== $namespace ) {
			if ( isset( $whitelist[ $namespace . '\\' . $className ] ) ) {
				return true;
			}
		} elseif ( isset( $whitelist[ $className ] ) ) {
			return true;
		}

		// Does the class/trait extend one of the whitelisted test classes ?
		$extendedClassName = $phpcsFile->findExtendedClassName( $stackPtr );
		if ( false === $extendedClassName ) {
			return false;
		}

		if ( '\\' === $extendedClassName[0] ) {
			if ( isset( $whitelist[ substr( $extendedClassName, 1 ) ] ) ) {
				return true;
			}
		} elseif ( '' !== $namespace ) {
			if ( isset( $whitelist[ $namespace . '\\' . $extendedClassName ] ) ) {
				return true;
			}
		} elseif ( isset( $whitelist[ $extendedClassName ] ) ) {
			return true;
		}

		/*
		 * Not examining imported classes via `use` statements as with the variety of syntaxes,
		 * this would get very complicated.
		 * After all, users can add an `<exclude-pattern>` for a particular sniff to their
		 * custom ruleset to selectively exclude the test directory.
		 */

		return false;
	}

}
