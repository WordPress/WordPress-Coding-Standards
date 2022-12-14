<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

use PHPCSUtils\BackCompat\Helper;

/**
 * Helper utilities for sniffs which take the minimum supported WP version of the
 * code under examination into account.
 *
 * Usage instructions:
 * - Add appropriate `use` statement(s) to the file/class which intends to use this functionality.
 * - Call the `MinimumWPVersionTrait::get_wp_version_from_cli()` method in the `process()`/`process_token()`
 *   method.
 * - After that, the `MinimumWPVersionTrait::$minimum_wp_version` property can be freely used
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
	 * `WordPress.WP.Capabilities`, `WordPress.WP.DeprecatedClasses`,
	 * `WordPress.WP.DeprecatedFunctions`, `WordPress.WP.DeprecatedParameter`
	 * and the `WordPress.WP.DeprecatedParameterValues` sniff.
	 *
	 * These sniffs will adapt their behaviour based on the minimum supported WP version
	 * indicated.
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
	 *   <property name="minimum_wp_version" value="4.9"/>
	 *  </properties>
	 * </rule>
	 *
	 * Alternatively, the value can be passed in one go for all sniffs using it via
	 * the command line or by setting a `<config>` value in a custom phpcs.xml ruleset.
	 *
	 * CL:      `phpcs --runtime-set minimum_wp_version 5.7`
	 * Ruleset: `<config name="minimum_wp_version" value="6.0"/>`
	 *
	 * @since 0.14.0 Previously the individual sniffs each contained this property.
	 * @since 3.0.0  - Moved from the Sniff class to this dedicated Trait.
	 *               - The property has been renamed from `$minimum_supported_version` to `$minimum_wp_version`.
	 *               - The CLI option has been renamed from `minimum_supported_wp_version` to `minimum_wp_version`.
	 *
	 * @internal When the value of this property is changed, it will also need
	 *           to be changed in the `WP/AlternativeFunctionsUnitTest.inc` file.
	 *
	 * @var string WordPress version.
	 */
	public $minimum_wp_version = '5.8';

	/**
	 * Overrule the minimum supported WordPress version with a command-line/config value.
	 *
	 * Handle setting the minimum supported WP version in one go for all sniffs which
	 * expect it via the command line or via a `<config>` variable in a ruleset.
	 * The config variable overrules the default `$minimum_wp_version` and/or a
	 * `$minimum_wp_version` set for individual sniffs through the ruleset.
	 *
	 * @since 0.14.0
	 * @since 3.0.0  - Moved from the Sniff class to this dedicated Trait.
	 *               - Renamed from `get_wp_version_from_cl()` to `get_wp_version_from_cli()`.
	 */
	protected function get_wp_version_from_cli() {
		$cli_supported_version = Helper::getConfigData( 'minimum_wp_version' );

		if ( empty( $cli_supported_version ) ) {
			return;
		}

		$cli_supported_version = trim( $cli_supported_version );
		if ( ! empty( $cli_supported_version )
			&& filter_var( $cli_supported_version, \FILTER_VALIDATE_FLOAT ) !== false
		) {
			$this->minimum_wp_version = $cli_supported_version;
		}
	}

	/**
	 * Compares two version numbers.
	 *
	 * Ensures that the version numbers are comparable via the PHP version_compare() function
	 * by making sure they comply with the minimum "PHP-standardized" version number requirements.
	 *
	 * @param string $version1 First version number.
	 * @param string $version2 Second version number.
	 * @param string $operator Comparison operator.
	 *
	 * @return bool
	 */
	protected function wp_version_compare( $version1, $version2, $operator ) {
		if ( preg_match( '`^\d+\.\d+$`', $version1 ) ) {
			$version1 .= '.0';
		}

		if ( preg_match( '`^\d+\.\d+$`', $version2 ) ) {
			$version2 .= '.0';
		}

		return version_compare( $version1, $version2, $operator );
	}
}
