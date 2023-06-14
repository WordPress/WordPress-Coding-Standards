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
use PHPCSUtils\Utils\ObjectDeclarations;
use WordPressCS\WordPress\Helpers\RulesetPropertyHelper;

/**
 * Helper utilities for sniffs which need to take into account whether the
 * code under examination is unit test code or not.
 *
 * Usage instructions:
 * - Add appropriate `use` statement(s) to the file/class which intends to use this functionality.
 * - Now the sniff will automatically support the public `custom_test_classes` property which
 *   users can set in their custom ruleset. Do not add the property to the sniff!
 * - The sniff can call the `is_test_class()` method in this trait to verify if a class is
 *   a test class. The `is_test_class()` method will take the custom property into account.
 *
 * @since 3.0.0 The properties and method in this trait were previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and have been moved here.
 */
trait IsUnitTestTrait {

	/**
	 * Custom list of classes which test classes can extend.
	 *
	 * This property allows end-users to add to the build-in `$known_test_classes`
	 * via their custom PHPCS ruleset.
	 * This property will need to be set for each sniff which uses this trait.
	 *
	 * Currently this property is used by the `WordPress.WP.GlobalVariablesOverride`,
	 * `WordPress.NamingConventions.PrefixAllGlobals` and the `WordPress.Files.Filename` sniffs.
	 *
	 * Example usage:
	 * ```xml
	 * <rule ref="WordPress.[Subset].[Sniffname]">
	 *  <properties>
	 *   <property name="custom_test_classes" type="array">
	 *     <element value="My_Plugin_First_Test_Class"/>
	 *     <element value="My_Plugin_Second_Test_Class"/>
	 *   </property>
	 *  </properties>
	 * </rule>
	 * ```
	 *
	 * Note: it is strongly _recommended_ to exclude your test directories for
	 * select error codes of those particular sniffs instead of relying on this
	 * property/trait.
	 *
	 * @since 0.11.0
	 * @since 3.0.0  Moved from the Sniff class to this dedicated Trait.
	 *               Renamed from `$custom_test_class_whitelist` to `$custom_test_classes`.
	 *
	 * @var string[]
	 */
	public $custom_test_classes = array();

	/**
	 * List of PHPUnit and WP native classes which test classes can extend.
	 *
	 * {internal These are the test cases provided in the `/tests/phpunit/includes/`
	 *           directory of WP Core.}
	 *
	 * @since 0.11.0
	 * @since 3.0.0  - Moved from the Sniff class to this dedicated Trait.
	 *               - Renamed from `$test_class_whitelist` to `$known_test_classes`.
	 *               - Visibility changed from protected to private.
	 *
	 * @var array<string, true> Key is class name, value irrelevant.
	 */
	private $known_test_classes = array(
		// Base test cases.
		'WP_UnitTestCase'                            => true,
		'WP_UnitTestCase_Base'                       => true,
		'PHPUnit_Adapter_TestCase'                   => true,

		// Domain specific base test cases.
		'WP_Ajax_UnitTestCase'                       => true,
		'WP_Canonical_UnitTestCase'                  => true,
		'WP_Test_REST_Controller_Testcase'           => true,
		'WP_Test_REST_Post_Type_Controller_Testcase' => true,
		'WP_Test_REST_TestCase'                      => true,
		'WP_Test_XML_TestCase'                       => true,
		'WP_XMLRPC_UnitTestCase'                     => true,

		// PHPUnit native test cases.
		'PHPUnit_Framework_TestCase'                 => true,
		'PHPUnit\\Framework\\TestCase'               => true,
		// PHPUnit native TestCase class when imported via use statement.
		'TestCase'                                   => true,
	);

	/**
	 * Cache of previously added custom test classes.
	 *
	 * Prevents having to do the same merges over and over again.
	 *
	 * @since 3.0.0
	 *
	 * @var string[]
	 */
	private $added_custom_test_classes = array();

	/**
	 * Combined list of WP/PHPUnit native and custom test classes.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, bool>
	 */
	private $all_test_classes = array();

	/**
	 * Retrieve a list of all registered test classes, both WP/PHPUnit native as well as custom.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, bool>
	 */
	final protected function get_all_test_classes() {
		if ( array() === $this->all_test_classes
			|| $this->custom_test_classes !== $this->added_custom_test_classes
		) {
			/*
			 * Show some tolerance for user input.
			 * The custom test class names should be passed as FQN without a prefixing `\`.
			 */
			$custom_test_classes = array();
			if ( ! empty( $this->custom_test_classes ) ) {
				foreach ( $this->custom_test_classes as $v ) {
					$custom_test_classes[] = ltrim( $v, '\\' );
				}
			}

			/*
			 * Lowercase all names, both custom as well as "known", as PHP treats namespaced names case-insensitively.
			 */
			$custom_test_classes = array_map( 'strtolower', $custom_test_classes );
			$known_test_classes  = array_change_key_case( $this->known_test_classes, \CASE_LOWER );

			$this->all_test_classes = RulesetPropertyHelper::merge_custom_array(
				$custom_test_classes,
				$known_test_classes
			);

			// Store the original value so the comparison can succeed.
			$this->added_custom_test_classes = $this->custom_test_classes;
		}

		return $this->all_test_classes;
	}

	/**
	 * Check if a class token is part of a unit test suite.
	 *
	 * Unit test classes are identified as such:
	 * - Class which either extends one of the known test cases, such as `WP_UnitTestCase`
	 *   or `PHPUnit_Framework_TestCase` or extends a custom unit test class as listed in the
	 *   `custom_test_classes` property.
	 *
	 * @since 0.12.0 Split off from the `is_token_in_test_method()` method.
	 * @since 1.0.0  Improved recognition of namespaced class names.
	 * @since 3.0.0  - Moved from the Sniff class to this dedicated Trait.
	 *               - The `$phpcsFile` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the token to be examined.
	 *                                               This should be a class, anonymous class or trait token.
	 *
	 * @return bool True if the class is a unit test class, false otherwise.
	 */
	final protected function is_test_class( File $phpcsFile, $stackPtr ) {

		$tokens = $phpcsFile->getTokens();

		if ( isset( $tokens[ $stackPtr ], Tokens::$ooScopeTokens[ $tokens[ $stackPtr ]['code'] ] ) === false ) {
			return false;
		}

		// Add any potentially extra custom test classes to the known test classes list.
		$known_test_classes = $this->get_all_test_classes();

		$namespace = strtolower( Namespaces::determineNamespace( $phpcsFile, $stackPtr ) );

		// Is the class/trait one of the known test classes ?
		$className = ObjectDeclarations::getName( $phpcsFile, $stackPtr );
		if ( empty( $className ) === false ) {
			$className = strtolower( $className );
			if ( '' !== $namespace ) {
				if ( isset( $known_test_classes[ $namespace . '\\' . $className ] ) ) {
					return true;
				}
			} elseif ( isset( $known_test_classes[ $className ] ) ) {
				return true;
			}
		}

		// Does the class/trait extend one of the known test classes ?
		$extendedClassName = ObjectDeclarations::findExtendedClassName( $phpcsFile, $stackPtr );
		if ( false === $extendedClassName ) {
			return false;
		}

		$extendedClassName = strtolower( $extendedClassName );

		if ( '\\' === $extendedClassName[0] ) {
			if ( isset( $known_test_classes[ substr( $extendedClassName, 1 ) ] ) ) {
				return true;
			}
		} elseif ( '' !== $namespace ) {
			if ( isset( $known_test_classes[ $namespace . '\\' . $extendedClassName ] ) ) {
				return true;
			}
		} elseif ( isset( $known_test_classes[ $extendedClassName ] ) ) {
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
