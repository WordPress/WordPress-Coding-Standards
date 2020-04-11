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
use PHPCSUtils\BackCompat\Helper;

/**
 * Helper utilities for sniffs which take the minimum supported WP version of the
 * code under examination into account.
 *
 * Usage instructions:
 * - Add appropriate `use` statement(s) to the file/class which intends to use this functionality.
 * - Call the `MinimumWPVersionTrait::get_wp_version_from_cl()` method in the `process()`/`process_token()`
 *   method.
 * - After that, the `MinimumWPVersionTrait::$minimum_supported_version` property can be freely used
 *   in the sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   3.0.0 The property and method in this trait were previously contained in the
 *                `WordPressCS\WordPress\Sniff` class and have been moved here.
 */
trait MinimumWPVersionTrait {

	/**
	 * Minimum supported WordPress version.
	 *
	 * Currently used by the `WordPress.WP.AlternativeFunctions`,
	 * `WordPress.WP.DeprecatedClasses`, `WordPress.WP.DeprecatedFunctions`,
	 * `WordPress.WP.DeprecatedParameter` and the `WordPress.WP.DeprecatedParameterValues` sniff.
	 *
	 * These sniffs will throw an error when usage of a deprecated class/function/parameter
	 * is detected if the class/function/parameter was deprecated before the minimum
	 * supported WP version; a warning otherwise.
	 * By default, it is set to presume that a project will support the current
	 * WP version and up to three releases before.
	 *
	 * This property allows changing the minimum supported WP version used by
	 * these sniffs by setting a property in a custom phpcs.xml ruleset.
	 * This property will need to be set for each sniff which uses it.
	 *
	 * Example usage:
	 * <rule ref="WordPress.WP.DeprecatedClasses">
	 *  <properties>
	 *   <property name="minimum_supported_version" value="4.3"/>
	 *  </properties>
	 * </rule>
	 *
	 * Alternatively, the value can be passed in one go for all sniff using it via
	 * the command line or by setting a `<config>` value in a custom phpcs.xml ruleset.
	 * Note: the `_wp_` in the command line property name!
	 *
	 * CL: `phpcs --runtime-set minimum_supported_wp_version 4.5`
	 * Ruleset: `<config name="minimum_supported_wp_version" value="4.5"/>`
	 *
	 * @since 0.14.0 Previously the individual sniffs each contained this property.
	 * @since 3.0.0  Moved from the Sniff class to this dedicated Trait.
	 *
	 * @internal When the value of this property is changed, it will also need
	 *           to be changed in the `WP/AlternativeFunctionsUnitTest.inc` file.
	 *
	 * @var string WordPress version.
	 */
	public $minimum_supported_version = '5.1';

	/**
	 * Overrule the minimum supported WordPress version with a command-line/config value.
	 *
	 * Handle setting the minimum supported WP version in one go for all sniffs which
	 * expect it via the command line or via a `<config>` variable in a ruleset.
	 * The config variable overrules the default `$minimum_supported_version` and/or a
	 * `$minimum_supported_version` set for individual sniffs through the ruleset.
	 *
	 * @since 0.14.0
	 * @since 3.0.0  Moved from the Sniff class to this dedicated Trait.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 */
	protected function get_wp_version_from_cl() {
		$cl_supported_version = trim( Helper::getConfigData( 'minimum_supported_wp_version' ) );
		if ( ! empty( $cl_supported_version )
			&& filter_var( $cl_supported_version, \FILTER_VALIDATE_FLOAT ) !== false
		) {
			$this->minimum_supported_version = $cl_supported_version;
		}
	}

}
