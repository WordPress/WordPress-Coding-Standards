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
 * - Call the `MinimumWPVersionTrait::set_minimum_wp_version()` method in the `process()`/`process_token()`
 *   method.
 * - After that, the `MinimumWPVersionTrait::$minimum_wp_version` property can be freely used
 *   in the sniff.
 *
 * @since 3.0.0 The property and method in this trait were previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and have been moved here.
 */
trait MinimumWPVersionTrait {

	/**
	 * Minimum supported WordPress version.
	 *
	 * Currently used by the `WordPress.Security.PreparedSQLPlaceholders`,
	 * `WordPress.WP.AlternativeFunctions`, `WordPress.WP.Capabilities`,
	 * `WordPress.WP.DeprecatedClasses`, `WordPress.WP.DeprecatedFunctions`,
	 * `WordPress.WP.DeprecatedParameter` and the `WordPress.WP.DeprecatedParameterValues` sniff.
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
	 * @var string WordPress version.
	 */
	public $minimum_wp_version;

	/**
	 * Default minimum supported WordPress version.
	 *
	 * By default, the minimum_wp_version presumes that a project will support the current
	 * WP version and up to three releases before.
	 *
	 * {@internal This should be a constant, but constants in traits are not supported
	 *            until PHP 8.2.}}
	 *
	 * @since 3.0.0
	 *
	 * @var string WordPress version.
	 */
	private $default_minimum_wp_version = '6.0';

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
	 *               - Renamed from `get_wp_version_from_cl()` to `set_minimum_wp_version()`.
	 *
	 * @return void
	 */
	final protected function set_minimum_wp_version() {
		$minimum_wp_version = '';

		// Use a ruleset provided value if available.
		if ( ! empty( $this->minimum_wp_version ) ) {
			$minimum_wp_version = $this->minimum_wp_version;
		}

		// A CLI provided value overrules a ruleset provided value.
		$cli_supported_version = Helper::getConfigData( 'minimum_wp_version' );
		if ( ! empty( $cli_supported_version ) ) {
			$minimum_wp_version = $cli_supported_version;
		}

		// If no valid value was provided, use the default.
		if ( filter_var( $minimum_wp_version, \FILTER_VALIDATE_FLOAT ) === false ) {
			$minimum_wp_version = $this->default_minimum_wp_version;
		}

		$this->minimum_wp_version = $minimum_wp_version;
	}

	/**
	 * Compares two version numbers.
	 *
	 * @since 3.0.0
	 *
	 * @param string $version1 First version number.
	 * @param string $version2 Second version number.
	 * @param string $operator Comparison operator.
	 *
	 * @return bool
	 */
	final protected function wp_version_compare( $version1, $version2, $operator ) {
		$version1 = $this->normalize_version_number( $version1 );
		$version2 = $this->normalize_version_number( $version2 );

		return version_compare( $version1, $version2, $operator );
	}

	/**
	 * Normalize a version number.
	 *
	 * Ensures that a version number is comparable via the PHP version_compare() function
	 * by making sure it complies with the minimum "PHP-standardized" version number requirements.
	 *
	 * Presumes the input is a numeric version number string. The behaviour with other input is undefined.
	 *
	 * @since 3.0.0
	 *
	 * @param string $version Version number.
	 *
	 * @return string
	 */
	private function normalize_version_number( $version ) {
		if ( preg_match( '`^\d+\.\d+$`', $version ) ) {
			$version .= '.0';
		}

		return $version;
	}
}
