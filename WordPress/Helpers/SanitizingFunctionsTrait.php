<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

use WordPressCS\WordPress\Helpers\RulesetPropertyHelper;

/**
 * Helper functions and function lists for checking whether a sanitizing function is being used.
 *
 * Any sniff class which incorporates this trait will automatically support the
 * following `public` properties which can be changed from within a custom ruleset:
 * - `customSanitizingFunctions`.
 * - `customUnslashingSanitizingFunctions`
 *
 * @since 3.0.0 The properties in this trait were previously contained partially in the
 *              `WordPressCS\WordPress\Sniff` class and partially in the `NonceVerificationSniff`
 *              and the `ValidatedSanitizedInputSniff` classes and have been moved here.
 */
trait SanitizingFunctionsTrait {

	/**
	 * Custom list of functions that sanitize the values passed to them.
	 *
	 * @since 0.5.0
	 * @since 3.0.0 Moved from the NonceVerification and the ValidatedSanitizedInput sniff classes to this trait.
	 *
	 * @var string[]
	 */
	public $customSanitizingFunctions = array();

	/**
	 * Custom sanitizing functions that implicitly unslash the values passed to them.
	 *
	 * @since 0.5.0
	 * @since 3.0.0 Moved from the NonceVerification and the ValidatedSanitizedInput sniff classes to this trait.
	 *
	 * @var string[]
	 */
	public $customUnslashingSanitizingFunctions = array();

	/**
	 * Functions that sanitize values.
	 *
	 * This list is complementary to the `$unslashingSanitizingFunctions`
	 * list.
	 * Sanitizing functions should be added to this list if they do *not*
	 * implicitly unslash data and to the `$unslashingsanitizingFunctions`
	 * list if they do.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0  - Moved from the Sniff class to this trait.
	 *               - Visibility changed from protected to private.
	 *
	 * @var array<string, bool>
	 */
	private $sanitizingFunctions = array(
		'_wp_handle_upload'          => true,
		'esc_url_raw'                => true,
		'filter_input'               => true,
		'filter_var'                 => true,
		'hash_equals'                => true,
		'is_email'                   => true,
		'number_format'              => true,
		'sanitize_bookmark_field'    => true,
		'sanitize_bookmark'          => true,
		'sanitize_email'             => true,
		'sanitize_file_name'         => true,
		'sanitize_hex_color_no_hash' => true,
		'sanitize_hex_color'         => true,
		'sanitize_html_class'        => true,
		'sanitize_meta'              => true,
		'sanitize_mime_type'         => true,
		'sanitize_option'            => true,
		'sanitize_sql_orderby'       => true,
		'sanitize_term_field'        => true,
		'sanitize_term'              => true,
		'sanitize_text_field'        => true,
		'sanitize_textarea_field'    => true,
		'sanitize_title_for_query'   => true,
		'sanitize_title_with_dashes' => true,
		'sanitize_title'             => true,
		'sanitize_url'               => true,
		'sanitize_user_field'        => true,
		'sanitize_user'              => true,
		'validate_file'              => true,
		'wp_handle_sideload'         => true,
		'wp_handle_upload'           => true,
		'wp_kses_allowed_html'       => true,
		'wp_kses_data'               => true,
		'wp_kses_one_attr'           => true,
		'wp_kses_post'               => true,
		'wp_kses'                    => true,
		'wp_parse_id_list'           => true,
		'wp_redirect'                => true,
		'wp_safe_redirect'           => true,
		'wp_sanitize_redirect'       => true,
		'wp_strip_all_tags'          => true,
	);

	/**
	 * Sanitizing functions that implicitly unslash the data passed to them.
	 *
	 * This list is complementary to the `$sanitizingFunctions` list.
	 * Sanitizing functions should be added to this list if they also
	 * implicitely unslash data and to the `$sanitizingFunctions` list
	 * if they don't.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0  - Moved from the Sniff class to this trait.
	 *               - Visibility changed from protected to private.
	 *
	 * @var array<string, bool>
	 */
	private $unslashingSanitizingFunctions = array(
		'absint'       => true,
		'boolval'      => true,
		'count'        => true,
		'doubleval'    => true,
		'floatval'     => true,
		'intval'       => true,
		'sanitize_key' => true,
		'sizeof'       => true,
	);

	/**
	 * Cache of previously added custom functions.
	 *
	 * Prevents having to do the same merges over and over again.
	 *
	 * @since 0.4.0
	 * @since 0.11.0 - Changed from public static to protected non-static.
	 *               - Changed the format from simple bool to array.
	 * @since 3.0.0  - Moved from the NonceVerification and the ValidatedSanitizedInput sniff classes to this class.
	 *               - Visibility changed from protected to private.
	 *
	 * @var array<string, string[]>
	 */
	private $addedCustomSanitizingFunctions = array(
		'sanitize'        => array(),
		'unslashsanitize' => array(),
	);

	/**
	 * Combined list of WP/PHP native and custom sanitizing functions.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, bool>
	 */
	private $allSanitizingFunctions = array();

	/**
	 * Combined list of WP/PHP native and custom sanitizing and unslashing functions.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, bool>
	 */
	private $allUnslashingSanitizingFunctions = array();

	/**
	 * Retrieve a list of all known sanitizing functions.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, bool>
	 */
	public function get_sanitizing_functions() {
		if ( array() === $this->allSanitizingFunctions
			|| $this->customSanitizingFunctions !== $this->addedCustomSanitizingFunctions['sanitize']
		) {
			$this->allSanitizingFunctions = RulesetPropertyHelper::merge_custom_array(
				$this->customSanitizingFunctions,
				$this->sanitizingFunctions
			);

			$this->addedCustomSanitizingFunctions['sanitize'] = $this->customSanitizingFunctions;
		}

		return $this->allSanitizingFunctions;
	}

	/**
	 * Retrieve a list of all known sanitizing and unslashing functions.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, bool>
	 */
	public function get_sanitizing_and_unslashing_functions() {
		if ( array() === $this->allUnslashingSanitizingFunctions
			|| $this->customUnslashingSanitizingFunctions !== $this->addedCustomSanitizingFunctions['unslashsanitize']
		) {
			$this->allUnslashingSanitizingFunctions = RulesetPropertyHelper::merge_custom_array(
				$this->customUnslashingSanitizingFunctions,
				$this->unslashingSanitizingFunctions
			);

			$this->addedCustomSanitizingFunctions['unslashsanitize'] = $this->customUnslashingSanitizingFunctions;
		}

		return $this->allUnslashingSanitizingFunctions;
	}

	/**
	 * Check if a particular function is regarded as a sanitizing function.
	 *
	 * @since 3.0.0
	 *
	 * @param string $functionName The name of the function to check.
	 *
	 * @return bool
	 */
	public function is_sanitizing_function( $functionName ) {
		return isset( $this->get_sanitizing_functions()[ strtolower( $functionName ) ] );
	}

	/**
	 * Check if a particular function is regarded as a sanitizing and unslashing function.
	 *
	 * @since 3.0.0
	 *
	 * @param string $functionName The name of the function to check.
	 *
	 * @return bool
	 */
	public function is_sanitizing_and_unslashing_function( $functionName ) {
		return isset( $this->get_sanitizing_and_unslashing_functions()[ strtolower( $functionName ) ] );
	}
}
