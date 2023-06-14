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
 * Helper functions and function lists for checking whether an escaping function is being used.
 *
 * Any sniff class which incorporates this trait will automatically support the
 * following `public` properties which can be changed from within a custom ruleset:
 * - `customEscapingFunctions`.
 * - `customAutoEscapedFunctions`
 *
 * @since 3.0.0 The properties in this trait were previously contained partially in the
 *              `WordPressCS\WordPress\Sniff` class and partially in the `EscapeOutputSniff`
 *              class and have been moved here.
 */
trait EscapingFunctionsTrait {

	/**
	 * Custom list of functions which escape values for display.
	 *
	 * @since 0.5.0
	 * @since 3.0.0 Moved from the EscapeOutput Sniff class to this trait.
	 *
	 * @var string[]
	 */
	public $customEscapingFunctions = array();

	/**
	 * Custom list of functions whose return values are pre-escaped for display.
	 *
	 * @since 0.3.0
	 * @since 3.0.0 Moved from the EscapeOutput Sniff class to this trait.
	 *
	 * @var string[]
	 */
	public $customAutoEscapedFunctions = array();

	/**
	 * Functions that escape values for display.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0 - Moved from the Sniff class to this trait.
	 *              - Visibility changed from protected to private.
	 *
	 * @var array<string, bool>
	 */
	private $escapingFunctions = array(
		'absint'                     => true,
		'esc_attr__'                 => true,
		'esc_attr_e'                 => true,
		'esc_attr_x'                 => true,
		'esc_attr'                   => true,
		'esc_html__'                 => true,
		'esc_html_e'                 => true,
		'esc_html_x'                 => true,
		'esc_html'                   => true,
		'esc_js'                     => true,
		'esc_sql'                    => true,
		'esc_textarea'               => true,
		'esc_url_raw'                => true,
		'esc_url'                    => true,
		'esc_xml'                    => true,
		'filter_input'               => true,
		'filter_var'                 => true,
		'floatval'                   => true,
		'highlight_string'           => true,
		'intval'                     => true,
		'json_encode'                => true,
		'like_escape'                => true,
		'number_format'              => true,
		'rawurlencode'               => true,
		'sanitize_hex_color'         => true,
		'sanitize_hex_color_no_hash' => true,
		'sanitize_html_class'        => true,
		'sanitize_key'               => true,
		'sanitize_user_field'        => true,
		'tag_escape'                 => true,
		'urlencode_deep'             => true,
		'urlencode'                  => true,
		'wp_json_encode'             => true,
		'wp_kses_allowed_html'       => true,
		'wp_kses_data'               => true,
		'wp_kses_one_attr'           => true,
		'wp_kses_post'               => true,
		'wp_kses'                    => true,
	);

	/**
	 * Functions whose output is automatically escaped for display.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0 - Moved from the Sniff class to this trait.
	 *              - Visibility changed from protected to private.
	 *
	 * @var array<string, bool>
	 */
	private $autoEscapedFunctions = array(
		'allowed_tags'            => true,
		'bloginfo'                => true,
		'body_class'              => true,
		'calendar_week_mod'       => true,
		'category_description'    => true,
		'checked'                 => true,
		'comment_class'           => true,
		'count'                   => true,
		'disabled'                => true,
		'do_shortcode'            => true,
		'do_shortcode_tag'        => true,
		'get_archives_link'       => true,
		'get_attachment_link'     => true,
		'get_avatar'              => true,
		'get_bookmark_field'      => true,
		'get_calendar'            => true,
		'get_comment_author_link' => true,
		'get_current_blog_id'     => true,
		'get_delete_post_link'    => true,
		'get_search_form'         => true,
		'get_search_query'        => true,
		'get_the_author_link'     => true,
		'get_the_author'          => true,
		'get_the_date'            => true,
		'get_the_ID'              => true,
		'get_the_post_thumbnail'  => true,
		'get_the_term_list'       => true,
		'post_type_archive_title' => true,
		'readonly'                => true,
		'selected'                => true,
		'single_cat_title'        => true,
		'single_month_title'      => true,
		'single_post_title'       => true,
		'single_tag_title'        => true,
		'single_term_title'       => true,
		'tag_description'         => true,
		'term_description'        => true,
		'the_author'              => true,
		'the_date'                => true,
		'the_title_attribute'     => true,
		'walk_nav_menu_tree'      => true,
		'wp_dropdown_categories'  => true,
		'wp_dropdown_users'       => true,
		'wp_generate_tag_cloud'   => true,
		'wp_get_archives'         => true,
		'wp_get_attachment_image' => true,
		'wp_get_attachment_link'  => true,
		'wp_link_pages'           => true,
		'wp_list_authors'         => true,
		'wp_list_bookmarks'       => true,
		'wp_list_categories'      => true,
		'wp_list_comments'        => true,
		'wp_login_form'           => true,
		'wp_loginout'             => true,
		'wp_nav_menu'             => true,
		'wp_readonly'             => true,
		'wp_register'             => true,
		'wp_tag_cloud'            => true,
		'wp_timezone_choice'      => true,
		'wp_title'                => true,
	);

	/**
	 * Cache of previously added custom functions.
	 *
	 * Prevents having to do the same merges over and over again.
	 *
	 * @since 0.4.0
	 * @since 0.11.0 - Changed from public static to protected non-static.
	 *               - Changed the format from simple bool to array.
	 * @since 3.0.0  - Moved from the EscapeOutput Sniff class to this trait.
	 *               - Visibility changed from protected to private.
	 *
	 * @var array<string, string[]>
	 */
	private $addedCustomEscapingFunctions = array(
		'escape'     => array(),
		'autoescape' => array(),
	);

	/**
	 * Combined list of WP native and custom escaping functions.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, bool>
	 */
	private $allEscapingFunctions = array();

	/**
	 * Combined list of WP native and custom auto-escaping functions.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, bool>
	 */
	private $allAutoEscapedFunctions = array();

	/**
	 * Check if a particular function is regarded as an escaping function.
	 *
	 * @since 3.0.0
	 *
	 * @param string $functionName The name of the function to check.
	 *
	 * @return bool
	 */
	final public function is_escaping_function( $functionName ) {
		if ( array() === $this->allEscapingFunctions
			|| $this->customEscapingFunctions !== $this->addedCustomEscapingFunctions['escape']
		) {
			$this->allEscapingFunctions = RulesetPropertyHelper::merge_custom_array(
				$this->customEscapingFunctions,
				$this->escapingFunctions
			);

			$this->addedCustomEscapingFunctions['escape'] = $this->customEscapingFunctions;
		}

		return isset( $this->allEscapingFunctions[ strtolower( $functionName ) ] );
	}

	/**
	 * Check if a particular function is regarded as an auto-escaped function.
	 *
	 * @since 3.0.0
	 *
	 * @param string $functionName The name of the function to check.
	 *
	 * @return bool
	 */
	final public function is_auto_escaped_function( $functionName ) {
		if ( array() === $this->allAutoEscapedFunctions
			|| $this->customAutoEscapedFunctions !== $this->addedCustomEscapingFunctions['autoescape']
		) {
			$this->allAutoEscapedFunctions = RulesetPropertyHelper::merge_custom_array(
				$this->customAutoEscapedFunctions,
				$this->autoEscapedFunctions
			);

			$this->addedCustomEscapingFunctions['autoescape'] = $this->customAutoEscapedFunctions;
		}

		return isset( $this->allAutoEscapedFunctions[ strtolower( $functionName ) ] );
	}
}
