<?php
/**
 * PHPCS cross-version compatibility helper class.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress;

use PHP_CodeSniffer_File as File;

/**
 * PHPCSHelper
 *
 * PHPCS cross-version compatibility helper class.
 *
 * Deals with files which cannot be aliased 1-on-1 as the original
 * class was split up into several classes.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.13.0
 */
class PHPCSHelper {

	/**
	 * Get the PHPCS version number.
	 *
	 * @since 0.13.0
	 *
	 * @return string
	 */
	public static function get_version() {
		if ( defined( '\PHP_CodeSniffer\Config::VERSION' ) ) {
			// PHPCS 3.x.
			return \PHP_CodeSniffer\Config::VERSION;
		} else {
			// PHPCS 2.x.
			return \PHP_CodeSniffer::VERSION;
		}
	}

	/**
	 * Pass config data to PHPCS.
	 *
	 * PHPCS cross-version compatibility helper.
	 *
	 * @since 0.13.0
	 *
	 * @param string      $key   The name of the config value.
	 * @param string|null $value The value to set. If null, the config entry
	 *                           is deleted, reverting it to the default value.
	 * @param boolean     $temp  Set this config data temporarily for this script run.
	 *                           This will not write the config data to the config file.
	 */
	public static function set_config_data( $key, $value, $temp = false ) {
		if ( method_exists( '\PHP_CodeSniffer\Config', 'setConfigData' ) ) {
			// PHPCS 3.x.
			\PHP_CodeSniffer\Config::setConfigData( $key, $value, $temp );
		} else {
			// PHPCS 2.x.
			\PHP_CodeSniffer::setConfigData( $key, $value, $temp );
		}
	}

	/**
	 * Get the value of a single PHPCS config key.
	 *
	 * @since 0.13.0
	 *
	 * @param string $key The name of the config value.
	 *
	 * @return string|null
	 */
	public static function get_config_data( $key ) {
		if ( method_exists( '\PHP_CodeSniffer\Config', 'getConfigData' ) ) {
			// PHPCS 3.x.
			return \PHP_CodeSniffer\Config::getConfigData( $key );
		} else {
			// PHPCS 2.x.
			return \PHP_CodeSniffer::getConfigData( $key );
		}
	}

	/**
	 * Get the tab width as passed to PHPCS from the command-line or the ruleset.
	 *
	 * @since 0.13.0
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being processed.
	 *
	 * @return int Tab width. Defaults to 4.
	 */
	public static function get_tab_width( File $phpcsFile ) {
		$tab_width = 4;

		if ( class_exists( '\PHP_CodeSniffer\Config' ) ) {
			// PHPCS 3.x.
			if ( isset( $phpcsFile->config->tabWidth ) && $phpcsFile->config->tabWidth > 0 ) {
				$tab_width = $phpcsFile->config->tabWidth;
			}
		} else {
			// PHPCS 2.x.
			$cli_values = $phpcsFile->phpcs->cli->getCommandLineValues();
			if ( isset( $cli_values['tabWidth'] ) && $cli_values['tabWidth'] > 0 ) {
				$tab_width = $cli_values['tabWidth'];
			}
		}

		return $tab_width;
	}

	/**
	 * Check whether the `--ignore-annotations` option has been used.
	 *
	 * @since 0.13.0
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile Optional. The current file being processed.
	 *
	 * @return bool True if annotations should be ignored, false otherwise.
	 */
	public static function ignore_annotations( File $phpcsFile = null ) {
		if ( class_exists( '\PHP_CodeSniffer\Config' ) ) {
			// PHPCS 3.x.
			if ( isset( $phpcsFile, $phpcsFile->config->annotations ) ) {
				return ! $phpcsFile->config->annotations;
			} else {
				$annotations = \PHP_CodeSniffer\Config::getConfigData( 'annotations' );
				if ( isset( $annotations ) ) {
					return ! $annotations;
				}
			}
		}

		// PHPCS 2.x does not support `--ignore-annotations`.
		return false;
	}

}
