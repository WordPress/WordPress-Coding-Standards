<?php
/**
 * Represents a PHP_CodeSniffer sniff for sniffing WordPress coding standards.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress;

use PHP_CodeSniffer\Sniffs\Sniff as PHPCS_Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\Lists;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\Scopes;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\Helpers\VariableHelper;

/**
 * Represents a PHP_CodeSniffer sniff for sniffing WordPress coding standards.
 *
 * Provides a bootstrap for the sniffs, to reduce code duplication.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.4.0
 *
 * {@internal This class contains numerous properties where the array format looks
 *            like `'string' => true`, i.e. the array item is set as the array key.
 *            This allows for sniffs to verify whether something is in one of these
 *            lists using `isset()` rather than `in_array()` which is a much more
 *            efficient (faster) check to execute and therefore improves the
 *            performance of the sniffs.
 *            The `true` value in those cases is used as a placeholder and has no
 *            meaning in and of itself.
 *            In the rare few cases where the array values *do* have meaning, this
 *            is documented in the property documentation.}}
 */
abstract class Sniff implements PHPCS_Sniff {

	/**
	 * List of the functions which verify nonces.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array
	 */
	protected $nonceVerificationFunctions = array(
		'wp_verify_nonce'     => true,
		'check_admin_referer' => true,
		'check_ajax_referer'  => true,
	);

	/**
	 * Functions that escape values for display.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array
	 */
	protected $escapingFunctions = array(
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
		'wp_kses_post'               => true,
		'wp_kses'                    => true,
	);

	/**
	 * Functions whose output is automatically escaped for display.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array
	 */
	protected $autoEscapedFunctions = array(
		'allowed_tags'              => true,
		'bloginfo'                  => true,
		'body_class'                => true,
		'calendar_week_mod'         => true,
		'category_description'      => true,
		'checked'                   => true,
		'comment_class'             => true,
		'count'                     => true,
		'disabled'                  => true,
		'do_shortcode'              => true,
		'do_shortcode_tag'          => true,
		'get_archives_link'         => true,
		'get_attachment_link'       => true,
		'get_avatar'                => true,
		'get_bookmark_field'        => true,
		'get_calendar'              => true,
		'get_comment_author_link'   => true,
		'get_current_blog_id'       => true,
		'get_delete_post_link'      => true,
		'get_search_form'           => true,
		'get_search_query'          => true,
		'get_the_author_link'       => true,
		'get_the_author'            => true,
		'get_the_date'              => true,
		'get_the_ID'                => true,
		'get_the_post_thumbnail'    => true,
		'get_the_term_list'         => true,
		'post_type_archive_title'   => true,
		'readonly'                  => true,
		'selected'                  => true,
		'single_cat_title'          => true,
		'single_month_title'        => true,
		'single_post_title'         => true,
		'single_tag_title'          => true,
		'single_term_title'         => true,
		'tag_description'           => true,
		'term_description'          => true,
		'the_author'                => true,
		'the_date'                  => true,
		'the_title_attribute'       => true,
		'walk_nav_menu_tree'        => true,
		'wp_dropdown_categories'    => true,
		'wp_dropdown_users'         => true,
		'wp_generate_tag_cloud'     => true,
		'wp_get_archives'           => true,
		'wp_get_attachment_image'   => true,
		'wp_get_attachment_link'    => true,
		'wp_link_pages'             => true,
		'wp_list_authors'           => true,
		'wp_list_bookmarks'         => true,
		'wp_list_categories'        => true,
		'wp_list_comments'          => true,
		'wp_login_form'             => true,
		'wp_loginout'               => true,
		'wp_nav_menu'               => true,
		'wp_register'               => true,
		'wp_tag_cloud'              => true,
		'wp_title'                  => true,
	);

	/**
	 * Functions that sanitize values.
	 *
	 * This list is complementary to the `$unslashingSanitizingFunctions`
	 * list.
	 * Sanitizing functions should be added to this list if they do *not*
	 * implicitely unslash data and to the `$unslashingsanitizingFunctions`
	 * list if they do.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array
	 */
	protected $sanitizingFunctions = array(
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
		'sanitize_user_field'        => true,
		'sanitize_user'              => true,
		'validate_file'              => true,
		'wp_handle_sideload'         => true,
		'wp_handle_upload'           => true,
		'wp_kses_allowed_html'       => true,
		'wp_kses_data'               => true,
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
	 *
	 * @var array
	 */
	protected $unslashingSanitizingFunctions = array(
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
	 * Functions which unslash the data passed to them.
	 *
	 * @since 2.1.0
	 *
	 * @var array
	 */
	protected $unslashingFunctions = array(
		'stripslashes_deep'              => true,
		'stripslashes_from_strings_only' => true,
		'wp_unslash'                     => true,
	);

	/**
	 * List of PHP native functions to test the type of a variable.
	 *
	 * Using these functions is safe in combination with superglobals without
	 * unslashing or sanitization.
	 *
	 * They should, however, not be regarded as unslashing or sanitization functions.
	 *
	 * @since 2.1.0
	 *
	 * @var array
	 */
	protected $typeTestFunctions = array(
		'is_array'     => true,
		'is_bool'      => true,
		'is_callable'  => true,
		'is_countable' => true,
		'is_double'    => true,
		'is_float'     => true,
		'is_int'       => true,
		'is_integer'   => true,
		'is_iterable'  => true,
		'is_long'      => true,
		'is_null'      => true,
		'is_numeric'   => true,
		'is_object'    => true,
		'is_real'      => true,
		'is_resource'  => true,
		'is_scalar'    => true,
		'is_string'    => true,
	);

	/**
	 * Token which when they preceed code indicate the value is safely casted.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $safe_casts = array(
		\T_INT_CAST    => true,
		\T_DOUBLE_CAST => true,
		\T_BOOL_CAST   => true,
		\T_UNSET_CAST  => true,
	);

	/**
	 * List of array functions which apply a callback to the array.
	 *
	 * These are often used for sanitization/escaping an array variable.
	 *
	 * Note: functions which alter the array by reference are not listed here on purpose.
	 * These cannot easily be used for sanitization as they can't be combined with unslashing.
	 * Similarly, they cannot be used for late escaping as the return value is a boolean, not
	 * the altered array.
	 *
	 * @since 2.1.0
	 *
	 * @var array <string function name> => <int parameter position of the callback parameter>
	 */
	protected $arrayWalkingFunctions = array(
		'array_map' => 1,
		'map_deep'  => 2,
	);

	/**
	 * Array functions to compare a $needle to a predefined set of values.
	 *
	 * If the value is set to an integer, the function needs to have at least that
	 * many parameters for it to be considered as a comparison.
	 *
	 * @since 2.1.0
	 *
	 * @var array <string function name> => <true|int>
	 */
	protected $arrayCompareFunctions = array(
		'in_array'     => true,
		'array_search' => true,
		'array_keys'   => 2,
	);

	/**
	 * Functions that format strings.
	 *
	 * These functions are often used for formatting values just before output, and
	 * it is common practice to escape the individual parameters passed to them as
	 * needed instead of escaping the entire result. This is especially true when the
	 * string being formatted contains HTML, which makes escaping the full result
	 * more difficult.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array
	 */
	protected $formattingFunctions = array(
		'array_fill' => true,
		'ent2ncr'    => true,
		'implode'    => true,
		'join'       => true,
		'nl2br'      => true,
		'sprintf'    => true,
		'vsprintf'   => true,
		'wp_sprintf' => true,
	);

	/**
	 * Functions which print output incorporating the values passed to them.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array
	 */
	protected $printingFunctions = array(
		'_deprecated_argument'    => true,
		'_deprecated_constructor' => true,
		'_deprecated_file'        => true,
		'_deprecated_function'    => true,
		'_deprecated_hook'        => true,
		'_doing_it_wrong'         => true,
		'_e'                      => true,
		'_ex'                     => true,
		'printf'                  => true,
		'trigger_error'           => true,
		'user_error'              => true,
		'vprintf'                 => true,
		'wp_die'                  => true,
		'wp_dropdown_pages'       => true,
	);

	/**
	 * Functions that escape values for use in SQL queries.
	 *
	 * @since 0.9.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array
	 */
	protected $SQLEscapingFunctions = array(
		'absint'      => true,
		'esc_sql'     => true,
		'floatval'    => true,
		'intval'      => true,
		'like_escape' => true,
	);

	/**
	 * Functions whose output is automatically escaped for use in SQL queries.
	 *
	 * @since 0.9.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array
	 */
	protected $SQLAutoEscapedFunctions = array(
		'count' => true,
	);

	/**
	 * A list of functions that get data from the cache.
	 *
	 * @since 0.6.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array
	 */
	protected $cacheGetFunctions = array(
		'wp_cache_get' => true,
	);

	/**
	 * A list of functions that set data in the cache.
	 *
	 * @since 0.6.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array
	 */
	protected $cacheSetFunctions = array(
		'wp_cache_set' => true,
		'wp_cache_add' => true,
	);

	/**
	 * A list of functions that delete data from the cache.
	 *
	 * @since 0.6.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array
	 */
	protected $cacheDeleteFunctions = array(
		'wp_cache_delete'         => true,
		'clean_attachment_cache'  => true,
		'clean_blog_cache'        => true,
		'clean_bookmark_cache'    => true,
		'clean_category_cache'    => true,
		'clean_comment_cache'     => true,
		'clean_network_cache'     => true,
		'clean_object_term_cache' => true,
		'clean_page_cache'        => true,
		'clean_post_cache'        => true,
		'clean_term_cache'        => true,
		'clean_user_cache'        => true,
	);

	/**
	 * A list of functions that invoke WP hooks (filters/actions).
	 *
	 * @since 0.10.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array
	 */
	protected $hookInvokeFunctions = array(
		'do_action'                => true,
		'do_action_ref_array'      => true,
		'do_action_deprecated'     => true,
		'apply_filters'            => true,
		'apply_filters_ref_array'  => true,
		'apply_filters_deprecated' => true,
	);

	/**
	 * A list of functions that are used to interact with the WP plugins API.
	 *
	 * @since 0.10.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array <string function name> => <int position of the hook name argument in function signature>
	 */
	protected $hookFunctions = array(
		'has_filter'         => 1,
		'add_filter'         => 1,
		'remove_filter'      => 1,
		'remove_all_filters' => 1,
		'doing_filter'       => 1, // Hook name optional.
		'has_action'         => 1,
		'add_action'         => 1,
		'doing_action'       => 1, // Hook name optional.
		'did_action'         => 1,
		'remove_action'      => 1,
		'remove_all_actions' => 1,
		'current_filter'     => 0, // No hook name argument.
	);

	/**
	 * List of global WP variables.
	 *
	 * @since 0.3.0
	 * @since 0.11.0 Changed visibility from public to protected.
	 * @since 0.12.0 Renamed from `$globals` to `$wp_globals` to be more descriptive.
	 * @since 0.12.0 Moved here from the WordPress.Variables.GlobalVariables sniff.
	 *
	 * @var array
	 */
	protected $wp_globals = array(
		'_links_add_base'                  => true,
		'_links_add_target'                => true,
		'_menu_item_sort_prop'             => true,
		'_nav_menu_placeholder'            => true,
		'_new_bundled_files'               => true,
		'_old_files'                       => true,
		'_parent_pages'                    => true,
		'_registered_pages'                => true,
		'_updated_user_settings'           => true,
		'_wp_additional_image_sizes'       => true,
		'_wp_admin_css_colors'             => true,
		'_wp_default_headers'              => true,
		'_wp_deprecated_widgets_callbacks' => true,
		'_wp_last_object_menu'             => true,
		'_wp_last_utility_menu'            => true,
		'_wp_menu_nopriv'                  => true,
		'_wp_nav_menu_max_depth'           => true,
		'_wp_post_type_features'           => true,
		'_wp_real_parent_file'             => true,
		'_wp_registered_nav_menus'         => true,
		'_wp_sidebars_widgets'             => true,
		'_wp_submenu_nopriv'               => true,
		'_wp_suspend_cache_invalidation'   => true,
		'_wp_theme_features'               => true,
		'_wp_using_ext_object_cache'       => true,
		'action'                           => true,
		'active_signup'                    => true,
		'admin_body_class'                 => true,
		'admin_page_hooks'                 => true,
		'all_links'                        => true,
		'allowedentitynames'               => true,
		'allowedposttags'                  => true,
		'allowedtags'                      => true,
		'auth_secure_cookie'               => true,
		'authordata'                       => true,
		'avail_post_mime_types'            => true,
		'avail_post_stati'                 => true,
		'blog_id'                          => true,
		'blog_title'                       => true,
		'blogname'                         => true,
		'cat'                              => true,
		'cat_id'                           => true,
		'charset_collate'                  => true,
		'comment'                          => true,
		'comment_alt'                      => true,
		'comment_depth'                    => true,
		'comment_status'                   => true,
		'comment_thread_alt'               => true,
		'comment_type'                     => true,
		'comments'                         => true,
		'compress_css'                     => true,
		'compress_scripts'                 => true,
		'concatenate_scripts'              => true,
		'content_width'                    => true,
		'current_blog'                     => true,
		'current_screen'                   => true,
		'current_site'                     => true,
		'current_user'                     => true,
		'currentcat'                       => true,
		'currentday'                       => true,
		'currentmonth'                     => true,
		'custom_background'                => true,
		'custom_image_header'              => true,
		'default_menu_order'               => true,
		'descriptions'                     => true,
		'domain'                           => true,
		'editor_styles'                    => true,
		'error'                            => true,
		'errors'                           => true,
		'EZSQL_ERROR'                      => true,
		'feeds'                            => true,
		'GETID3_ERRORARRAY'                => true,
		'hook_suffix'                      => true,
		'HTTP_RAW_POST_DATA'               => true,
		'id'                               => true,
		'in_comment_loop'                  => true,
		'interim_login'                    => true,
		'is_apache'                        => true,
		'is_chrome'                        => true,
		'is_gecko'                         => true,
		'is_IE'                            => true,
		'is_IIS'                           => true,
		'is_iis7'                          => true,
		'is_macIE'                         => true,
		'is_NS4'                           => true,
		'is_opera'                         => true,
		'is_safari'                        => true,
		'is_winIE'                         => true,
		'l10n'                             => true,
		'link'                             => true,
		'link_id'                          => true,
		'locale'                           => true,
		'locked_post_status'               => true,
		'lost'                             => true,
		'm'                                => true,
		'map'                              => true,
		'menu'                             => true,
		'menu_order'                       => true,
		'merged_filters'                   => true,
		'mode'                             => true,
		'monthnum'                         => true,
		'more'                             => true,
		'mu_plugin'                        => true,
		'multipage'                        => true,
		'names'                            => true,
		'nav_menu_selected_id'             => true,
		'network_plugin'                   => true,
		'new_whitelist_options'            => true,
		'numpages'                         => true,
		'one_theme_location_no_menus'      => true,
		'opml'                             => true,
		'order'                            => true,
		'orderby'                          => true,
		'overridden_cpage'                 => true,
		'page'                             => true,
		'paged'                            => true,
		'pagenow'                          => true,
		'pages'                            => true,
		'parent_file'                      => true,
		'pass_allowed_html'                => true,
		'pass_allowed_protocols'           => true,
		'path'                             => true,
		'per_page'                         => true,
		'PHP_SELF'                         => true,
		'phpmailer'                        => true,
		'plugin_page'                      => true,
		'plugin'                           => true,
		'plugins'                          => true,
		'post'                             => true,
		'post_default_category'            => true,
		'post_default_title'               => true,
		'post_ID'                          => true,
		'post_id'                          => true,
		'post_mime_types'                  => true,
		'post_type'                        => true,
		'post_type_object'                 => true,
		'posts'                            => true,
		'preview'                          => true,
		'previouscat'                      => true,
		'previousday'                      => true,
		'previousweekday'                  => true,
		'redir_tab'                        => true,
		'required_mysql_version'           => true,
		'required_php_version'             => true,
		'rnd_value'                        => true,
		'role'                             => true,
		's'                                => true,
		'search'                           => true,
		'self'                             => true,
		'shortcode_tags'                   => true,
		'show_admin_bar'                   => true,
		'sidebars_widgets'                 => true,
		'status'                           => true,
		'submenu'                          => true,
		'submenu_file'                     => true,
		'super_admins'                     => true,
		'tab'                              => true,
		'table_prefix'                     => true,
		'tabs'                             => true,
		'tag'                              => true,
		'tag_ID'                           => true,
		'targets'                          => true,
		'tax'                              => true,
		'taxnow'                           => true,
		'taxonomy'                         => true,
		'term'                             => true,
		'text_direction'                   => true,
		'theme_field_defaults'             => true,
		'themes_allowedtags'               => true,
		'timeend'                          => true,
		'timestart'                        => true,
		'tinymce_version'                  => true,
		'title'                            => true,
		'totals'                           => true,
		'type'                             => true,
		'typenow'                          => true,
		'updated_timestamp'                => true,
		'upgrading'                        => true,
		'urls'                             => true,
		'user_email'                       => true,
		'user_ID'                          => true,
		'user_identity'                    => true,
		'user_level'                       => true,
		'user_login'                       => true,
		'user_url'                         => true,
		'userdata'                         => true,
		'usersearch'                       => true,
		'whitelist_options'                => true,
		'withcomments'                     => true,
		'wp'                               => true,
		'wp_actions'                       => true,
		'wp_admin_bar'                     => true,
		'wp_cockneyreplace'                => true,
		'wp_current_db_version'            => true,
		'wp_current_filter'                => true,
		'wp_customize'                     => true,
		'wp_dashboard_control_callbacks'   => true,
		'wp_db_version'                    => true,
		'wp_did_header'                    => true,
		'wp_embed'                         => true,
		'wp_file_descriptions'             => true,
		'wp_filesystem'                    => true,
		'wp_filter'                        => true,
		'wp_hasher'                        => true,
		'wp_header_to_desc'                => true,
		'wp_importers'                     => true,
		'wp_json'                          => true,
		'wp_list_table'                    => true,
		'wp_local_package'                 => true,
		'wp_locale'                        => true,
		'wp_meta_boxes'                    => true,
		'wp_object_cache'                  => true,
		'wp_plugin_paths'                  => true,
		'wp_post_statuses'                 => true,
		'wp_post_types'                    => true,
		'wp_queries'                       => true,
		'wp_query'                         => true,
		'wp_registered_sidebars'           => true,
		'wp_registered_widget_controls'    => true,
		'wp_registered_widget_updates'     => true,
		'wp_registered_widgets'            => true,
		'wp_rewrite'                       => true,
		'wp_rich_edit'                     => true,
		'wp_rich_edit_exists'              => true,
		'wp_roles'                         => true,
		'wp_scripts'                       => true,
		'wp_settings_errors'               => true,
		'wp_settings_fields'               => true,
		'wp_settings_sections'             => true,
		'wp_smiliessearch'                 => true,
		'wp_styles'                        => true,
		'wp_taxonomies'                    => true,
		'wp_the_query'                     => true,
		'wp_theme_directories'             => true,
		'wp_themes'                        => true,
		'wp_user_roles'                    => true,
		'wp_version'                       => true,
		'wp_widget_factory'                => true,
		'wp_xmlrpc_server'                 => true,
		'wpcommentsjavascript'             => true,
		'wpcommentspopupfile'              => true,
		'wpdb'                             => true,
		'wpsmiliestrans'                   => true,
		'year'                             => true,
	);

	/**
	 * A list of superglobals that incorporate user input.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from static to non-static.
	 *
	 * @var string[]
	 */
	protected $input_superglobals = array(
		'$_COOKIE',
		'$_GET',
		'$_FILES',
		'$_POST',
		'$_REQUEST',
		'$_SERVER',
	);

	/**
	 * The current file being sniffed.
	 *
	 * @since 0.4.0
	 *
	 * @var \PHP_CodeSniffer\Files\File
	 */
	protected $phpcsFile;

	/**
	 * The list of tokens in the current file being sniffed.
	 *
	 * @since 0.4.0
	 *
	 * @var array
	 */
	protected $tokens;

	/**
	 * Set sniff properties and hand off to child class for processing of the token.
	 *
	 * @since 0.11.0
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the current token
	 *                                               in the stack passed in $tokens.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$this->init( $phpcsFile );
		return $this->process_token( $stackPtr );
	}

	/**
	 * Processes a sniff when one of its tokens is encountered.
	 *
	 * @since 0.11.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	abstract public function process_token( $stackPtr );

	/**
	 * Initialize the class for the current process.
	 *
	 * This method must be called by child classes before using many of the methods
	 * below.
	 *
	 * @since 0.4.0
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file currently being processed.
	 */
	protected function init( File $phpcsFile ) {
		$this->phpcsFile = $phpcsFile;
		$this->tokens    = $phpcsFile->getTokens();
	}

	/**
	 * Add a PHPCS message to the output stack as either a warning or an error.
	 *
	 * @since 0.11.0
	 *
	 * @param string $message  The message.
	 * @param int    $stackPtr The position of the token the message relates to.
	 * @param bool   $is_error Optional. Whether to report the message as an 'error' or 'warning'.
	 *                         Defaults to true (error).
	 * @param string $code     Optional error code for the message. Defaults to 'Found'.
	 * @param array  $data     Optional input for the data replacements.
	 * @param int    $severity Optional. Severity level. Defaults to 0 which will translate to
	 *                         the PHPCS default severity level.
	 * @return bool
	 */
	protected function addMessage( $message, $stackPtr, $is_error = true, $code = 'Found', $data = array(), $severity = 0 ) {
		return $this->throwMessage( $message, $stackPtr, $is_error, $code, $data, $severity, false );
	}

	/**
	 * Add a fixable PHPCS message to the output stack as either a warning or an error.
	 *
	 * @since 0.11.0
	 *
	 * @param string $message  The message.
	 * @param int    $stackPtr The position of the token the message relates to.
	 * @param bool   $is_error Optional. Whether to report the message as an 'error' or 'warning'.
	 *                         Defaults to true (error).
	 * @param string $code     Optional error code for the message. Defaults to 'Found'.
	 * @param array  $data     Optional input for the data replacements.
	 * @param int    $severity Optional. Severity level. Defaults to 0 which will translate to
	 *                         the PHPCS default severity level.
	 * @return bool
	 */
	protected function addFixableMessage( $message, $stackPtr, $is_error = true, $code = 'Found', $data = array(), $severity = 0 ) {
		return $this->throwMessage( $message, $stackPtr, $is_error, $code, $data, $severity, true );
	}

	/**
	 * Add a PHPCS message to the output stack as either a warning or an error.
	 *
	 * @since 0.11.0
	 *
	 * @param string $message  The message.
	 * @param int    $stackPtr The position of the token the message relates to.
	 * @param bool   $is_error Optional. Whether to report the message as an 'error' or 'warning'.
	 *                         Defaults to true (error).
	 * @param string $code     Optional error code for the message. Defaults to 'Found'.
	 * @param array  $data     Optional input for the data replacements.
	 * @param int    $severity Optional. Severity level. Defaults to 0 which will translate to
	 *                         the PHPCS default severity level.
	 * @param bool   $fixable  Optional. Whether this is a fixable error. Defaults to false.
	 * @return bool
	 */
	private function throwMessage( $message, $stackPtr, $is_error = true, $code = 'Found', $data = array(), $severity = 0, $fixable = false ) {

		$method = 'add';
		if ( true === $fixable ) {
			$method .= 'Fixable';
		}

		if ( true === $is_error ) {
			$method .= 'Error';
		} else {
			$method .= 'Warning';
		}

		return \call_user_func( array( $this->phpcsFile, $method ), $message, $stackPtr, $code, $data, $severity );
	}

	/**
	 * Convert an arbitrary string to an alphanumeric string with underscores.
	 *
	 * Pre-empt issues with arbitrary strings being used as error codes in XML and PHP.
	 *
	 * @since 0.11.0
	 *
	 * @param string $base_string Arbitrary string.
	 *
	 * @return string
	 */
	protected function string_to_errorcode( $base_string ) {
		return preg_replace( '`[^a-z0-9_]`i', '_', $base_string );
	}

	/**
	 * Transform the name of a PHP construct (function, variable etc) to one in snake_case.
	 *
	 * @since 2.0.0 Moved from the `WordPress.NamingConventions.ValidFunctionName` sniff
	 *              to this class, renamed from `get_name_suggestion` and made static
	 *              so it can also be used by classes which don't extend this class.
	 *
	 * @param string $name The construct name.
	 *
	 * @return string
	 */
	public static function get_snake_case_name_suggestion( $name ) {
		$suggested = preg_replace( '`([A-Z])`', '_$1', $name );
		$suggested = strtolower( $suggested );
		$suggested = str_replace( '__', '_', $suggested );
		$suggested = trim( $suggested, '_' );
		return $suggested;
	}

	/**
	 * Merge a pre-set array with a ruleset provided array.
	 *
	 * - By default flips custom lists to allow for using `isset()` instead
	 *   of `in_array()`.
	 * - When `$flip` is true:
	 *   * Presumes the base array is in a `'value' => true` format.
	 *   * Any custom items will be given the value `false` to be able to
	 *     distinguish them from pre-set (base array) values.
	 *   * Will filter previously added custom items out from the base array
	 *     before merging/returning to allow for resetting to the base array.
	 *
	 * {@internal Function is static as it doesn't use any of the properties or others
	 * methods anyway and this way the `WordPress.NamingConventions.ValidVariableName` sniff
	 * which extends an upstream sniff can also use it.}}
	 *
	 * @since 0.11.0
	 * @since 2.0.0  No longer supports custom array properties which were incorrectly
	 *               passed as a string.
	 *
	 * @param array $custom Custom list as provided via a ruleset.
	 * @param array $base   Optional. Base list. Defaults to an empty array.
	 *                      Expects `value => true` format when `$flip` is true.
	 * @param bool  $flip   Optional. Whether or not to flip the custom list.
	 *                      Defaults to true.
	 * @return array
	 */
	public static function merge_custom_array( $custom, $base = array(), $flip = true ) {
		if ( true === $flip ) {
			$base = array_filter( $base );
		}

		if ( empty( $custom ) || ! \is_array( $custom ) ) {
			return $base;
		}

		if ( true === $flip ) {
			$custom = array_fill_keys( $custom, false );
		}

		if ( empty( $base ) ) {
			return $custom;
		}

		return array_merge( $base, $custom );
	}

	/**
	 * Get the last pointer in a line.
	 *
	 * @since 0.4.0
	 *
	 * @param integer $stackPtr The position of the current token in the stack passed
	 *                          in $tokens.
	 *
	 * @return integer Position of the last pointer on that line.
	 */
	protected function get_last_ptr_on_line( $stackPtr ) {

		$tokens      = $this->tokens;
		$currentLine = $tokens[ $stackPtr ]['line'];
		$nextPtr     = ( $stackPtr + 1 );

		while ( isset( $tokens[ $nextPtr ] ) && $tokens[ $nextPtr ]['line'] === $currentLine ) {
			$nextPtr++;
			// Do nothing, we just want the last token of the line.
		}

		// We've made it to the next line, back up one to the last in the previous line.
		// We do this for micro-optimization of the above loop.
		return ( $nextPtr - 1 );
	}

	/**
	 * Check if this variable is being assigned a value.
	 *
	 * E.g., $var = 'foo';
	 *
	 * Also handles array assignments to arbitrary depth:
	 *
	 * $array['key'][ $foo ][ something() ] = $bar;
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack. This must point to
	 *                      either a T_VARIABLE or T_CLOSE_SQUARE_BRACKET token.
	 *
	 * @return bool Whether the token is a variable being assigned a value.
	 */
	protected function is_assignment( $stackPtr ) {

		static $valid = array(
			\T_VARIABLE             => true,
			\T_CLOSE_SQUARE_BRACKET => true,
		);

		// Must be a variable, constant or closing square bracket (see below).
		if ( ! isset( $valid[ $this->tokens[ $stackPtr ]['code'] ] ) ) {
			return false;
		}

		$next_non_empty = $this->phpcsFile->findNext(
			Tokens::$emptyTokens,
			( $stackPtr + 1 ),
			null,
			true,
			null,
			true
		);

		// No token found.
		if ( false === $next_non_empty ) {
			return false;
		}

		// If the next token is an assignment, that's all we need to know.
		if ( isset( Tokens::$assignmentTokens[ $this->tokens[ $next_non_empty ]['code'] ] ) ) {
			return true;
		}

		// Check if this is an array assignment, e.g., `$var['key'] = 'val';` .
		if ( \T_OPEN_SQUARE_BRACKET === $this->tokens[ $next_non_empty ]['code']
			&& isset( $this->tokens[ $next_non_empty ]['bracket_closer'] )
		) {
			return $this->is_assignment( $this->tokens[ $next_non_empty ]['bracket_closer'] );
		}

		return false;
	}

	/**
	 * Check if this token has an associated nonce check.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The position of the current token in the stack of tokens.
	 *
	 * @return bool
	 */
	protected function has_nonce_check( $stackPtr ) {

		/**
		 * A cache of the scope that we last checked for nonce verification in.
		 *
		 * @var array {
		 *      @var string   $file        The name of the file.
		 *      @var int      $start       The index of the token where the scope started.
		 *      @var int      $end         The index of the token where the scope ended.
		 *      @var bool|int $nonce_check The index of the token where an nonce check
		 *                                 was found, or false if none was found.
		 * }
		 */
		static $last;

		$start = 0;
		$end   = $stackPtr;

		$tokens = $this->phpcsFile->getTokens();

		// If we're in a function, only look inside of it.
		// Once PHPCS 3.5.0 comes out this should be changed to the new Conditions::GetLastCondition() method.
		if ( isset( $tokens[ $stackPtr ]['conditions'] ) === true ) {
			$conditions = $tokens[ $stackPtr ]['conditions'];
			$conditions = array_reverse( $conditions, true );
			foreach ( $conditions as $tokenPtr => $condition ) {
				if ( \T_FUNCTION === $condition || \T_CLOSURE === $condition ) {
					$start = $tokens[ $tokenPtr ]['scope_opener'];
					break;
				}
			}
		}

		$allow_nonce_after = false;
		if ( $this->is_in_isset_or_empty( $stackPtr )
			|| $this->is_in_type_test( $stackPtr )
			|| VariableHelper::is_comparison( $this->phpcsFile, $stackPtr )
			|| $this->is_in_array_comparison( $stackPtr )
			|| $this->is_in_function_call( $stackPtr, $this->unslashingFunctions ) !== false
			|| $this->is_only_sanitized( $stackPtr )
		) {
			$allow_nonce_after = true;
		}

		// We allow for certain actions, such as an isset() check to come before the nonce check.
		// If this superglobal is inside such a check, look for the nonce after it as well,
		// all the way to the end of the scope.
		if ( true === $allow_nonce_after ) {
			$end = ( 0 === $start ) ? $this->phpcsFile->numTokens : $tokens[ $start ]['scope_closer'];
		}

		// Check if we've looked here before.
		$filename = $this->phpcsFile->getFilename();

		if ( is_array( $last )
			&& $filename === $last['file']
			&& $start === $last['start']
		) {

			if ( false !== $last['nonce_check'] ) {
				// If we have already found an nonce check in this scope, we just
				// need to check whether it comes before this token. It is OK if the
				// check is after the token though, if this was only a isset() check.
				return ( true === $allow_nonce_after || $last['nonce_check'] < $stackPtr );
			} elseif ( $end <= $last['end'] ) {
				// If not, we can still go ahead and return false if we've already
				// checked to the end of the search area.
				return false;
			}

			// We haven't checked this far yet, but we can still save work by
			// skipping over the part we've already checked.
			$start = $last['end'];
		} else {
			$last = array(
				'file'  => $filename,
				'start' => $start,
				'end'   => $end,
			);
		}

		// Loop through the tokens looking for nonce verification functions.
		for ( $i = $start; $i < $end; $i++ ) {
			// Skip over nested closed scope constructs.
			if ( \T_FUNCTION === $tokens[ $i ]['code']
				|| \T_CLOSURE === $tokens[ $i ]['code']
				|| isset( Tokens::$ooScopeTokens[ $tokens[ $i ]['code'] ] )
			) {
				if ( isset( $tokens[ $i ]['scope_closer'] ) ) {
					$i = $tokens[ $i ]['scope_closer'];
				}
				continue;
			}

			// If this isn't a function name, skip it.
			if ( \T_STRING !== $tokens[ $i ]['code'] ) {
				continue;
			}

			// If this is one of the nonce verification functions, we can bail out.
			if ( isset( $this->nonceVerificationFunctions[ $tokens[ $i ]['content'] ] ) ) {
				/*
				 * Now, make sure it is a call to a global function.
				 */
				if ( $this->is_class_object_call( $i ) === true ) {
					continue;
				}

				if ( $this->is_token_namespaced( $i ) === true ) {
					continue;
				}

				$last['nonce_check'] = $i;
				return true;
			}
		}

		// We're still here, so no luck.
		$last['nonce_check'] = false;

		return false;
	}

	/**
	 * Check if a token is inside of an isset(), empty() or array_key_exists() statement.
	 *
	 * @since 0.5.0
	 * @since 2.1.0 Now checks for the token being used as the array parameter
	 *              in function calls to array_key_exists() and key_exists() as well.
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool Whether the token is inside an isset() or empty() statement.
	 */
	protected function is_in_isset_or_empty( $stackPtr ) {

		if ( ! isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			return false;
		}

		$nested_parenthesis = $this->tokens[ $stackPtr ]['nested_parenthesis'];

		end( $nested_parenthesis );
		$open_parenthesis = key( $nested_parenthesis );

		$previous_non_empty = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $open_parenthesis - 1 ), null, true, null, true );
		if ( false === $previous_non_empty ) {
			return false;
		}

		$previous_code = $this->tokens[ $previous_non_empty ]['code'];
		if ( \T_ISSET === $previous_code || \T_EMPTY === $previous_code ) {
			return true;
		}

		$valid_functions = array(
			'array_key_exists' => true,
			'key_exists'       => true, // Alias.
		);

		$functionPtr = $this->is_in_function_call( $stackPtr, $valid_functions );
		if ( false !== $functionPtr ) {
			$second_param = PassedParameters::getParameter( $this->phpcsFile, $functionPtr, 2 );
			if ( $stackPtr >= $second_param['start'] && $stackPtr <= $second_param['end'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a particular token is a (static or non-static) call to a class method or property.
	 *
	 * @internal Note: this may still mistake a namespaced function imported via a `use` statement for
	 * a global function!
	 *
	 * @since 2.1.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool
	 */
	protected function is_class_object_call( $stackPtr ) {
		$before = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true, null, true );

		if ( false === $before ) {
			return false;
		}

		if ( \T_OBJECT_OPERATOR !== $this->tokens[ $before ]['code']
			&& \T_DOUBLE_COLON !== $this->tokens[ $before ]['code']
		) {
			return false;
		}

		return true;
	}

	/**
	 * Check if a particular token is prefixed with a namespace.
	 *
	 * @internal This will give a false positive if the file is not namespaced and the token is prefixed
	 * with `namespace\`.
	 *
	 * @since 2.1.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool
	 */
	protected function is_token_namespaced( $stackPtr ) {
		$prev = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true, null, true );

		if ( false === $prev ) {
			return false;
		}

		if ( \T_NS_SEPARATOR !== $this->tokens[ $prev ]['code'] ) {
			return false;
		}

		$before_prev = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $prev - 1 ), null, true, null, true );
		if ( false === $before_prev ) {
			return false;
		}

		if ( \T_STRING !== $this->tokens[ $before_prev ]['code']
			&& \T_NAMESPACE !== $this->tokens[ $before_prev ]['code']
		) {
			return false;
		}

		return true;
	}

	/**
	 * Check if a token is (part of) a parameter for a function call to a select list of functions.
	 *
	 * This is useful, for instance, when trying to determine the context a variable is used in.
	 *
	 * For example: this function could be used to determine if the variable `$foo` is used
	 * in a global function call to the function `is_foo()`.
	 * In that case, a call to this function would return the stackPtr to the T_STRING `is_foo`
	 * for code like: `is_foo( $foo, 'some_other_param' )`, while it would return `false` for
	 * the following code `is_bar( $foo, 'some_other_param' )`.
	 *
	 * @since 2.1.0
	 *
	 * @param int   $stackPtr        The index of the token in the stack.
	 * @param array $valid_functions List of valid function names.
	 *                               Note: The keys to this array should be the function names
	 *                               in lowercase. Values are irrelevant.
	 * @param bool  $global          Optional. Whether to make sure that the function call is
	 *                               to a global function. If `false`, calls to methods, be it static
	 *                               `Class::method()` or via an object `$obj->method()`, and
	 *                               namespaced function calls, like `MyNS\function_name()` will
	 *                               also be accepted.
	 *                               Defaults to `true`.
	 * @param bool  $allow_nested    Optional. Whether to allow for nested function calls within the
	 *                               call to this function.
	 *                               I.e. when checking whether a token is within a function call
	 *                               to `strtolower()`, whether to accept `strtolower( trim( $var ) )`
	 *                               or only `strtolower( $var )`.
	 *                               Defaults to `false`.
	 *
	 * @return int|bool Stack pointer to the function call T_STRING token or false otherwise.
	 */
	protected function is_in_function_call( $stackPtr, $valid_functions, $global = true, $allow_nested = false ) {
		if ( ! isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			return false;
		}

		$nested_parenthesis = $this->tokens[ $stackPtr ]['nested_parenthesis'];
		if ( false === $allow_nested ) {
			$nested_parenthesis = array_reverse( $nested_parenthesis, true );
		}

		foreach ( $nested_parenthesis as $open => $close ) {

			$prev_non_empty = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $open - 1 ), null, true, null, true );
			if ( false === $prev_non_empty || \T_STRING !== $this->tokens[ $prev_non_empty ]['code'] ) {
				continue;
			}

			if ( isset( $valid_functions[ strtolower( $this->tokens[ $prev_non_empty ]['content'] ) ] ) === false ) {
				if ( false === $allow_nested ) {
					// Function call encountered, but not to one of the allowed functions.
					return false;
				}

				continue;
			}

			if ( false === $global ) {
				return $prev_non_empty;
			}

			/*
			 * Now, make sure it is a global function.
			 */
			if ( $this->is_class_object_call( $prev_non_empty ) === true ) {
				continue;
			}

			if ( $this->is_token_namespaced( $prev_non_empty ) === true ) {
				continue;
			}

			return $prev_non_empty;
		}

		return false;
	}

	/**
	 * Check if a token is inside of an is_...() statement.
	 *
	 * @since 2.1.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool Whether the token is being type tested.
	 */
	protected function is_in_type_test( $stackPtr ) {
		/*
		 * Casting the potential integer stack pointer return value to boolean here is fine.
		 * The return can never be `0` as there will always be a PHP open tag before the
		 * function call.
		 */
		return (bool) $this->is_in_function_call( $stackPtr, $this->typeTestFunctions );
	}

	/**
	 * Check if something is only being sanitized.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool Whether the token is only within a sanitization.
	 */
	protected function is_only_sanitized( $stackPtr ) {

		// If it isn't being sanitized at all.
		if ( ! $this->is_sanitized( $stackPtr ) ) {
			return false;
		}

		// If this isn't set, we know the value must have only been casted, because
		// is_sanitized() would have returned false otherwise.
		if ( ! isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			return true;
		}

		// At this point we're expecting the value to have not been casted. If it
		// was, it wasn't *only* casted, because it's also in a function.
		if ( $this->is_safe_casted( $stackPtr ) ) {
			return false;
		}

		// The only parentheses should belong to the sanitizing function. If there's
		// more than one set, this isn't *only* sanitization.
		return ( \count( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) === 1 );
	}

	/**
	 * Check if something is being casted to a safe value.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool Whether the token being casted.
	 */
	protected function is_safe_casted( $stackPtr ) {

		// Get the last non-empty token.
		$prev = $this->phpcsFile->findPrevious(
			Tokens::$emptyTokens,
			( $stackPtr - 1 ),
			null,
			true
		);

		if ( false === $prev ) {
			return false;
		}

		// Check if it is a safe cast.
		return isset( $this->safe_casts[ $this->tokens[ $prev ]['code'] ] );
	}

	/**
	 * Check if something is being sanitized.
	 *
	 * @since 0.5.0
	 *
	 * @param int  $stackPtr        The index of the token in the stack.
	 * @param bool $require_unslash Whether to give an error if no unslashing function
	 *                              is used on the variable before sanitization.
	 *
	 * @return bool Whether the token being sanitized.
	 */
	protected function is_sanitized( $stackPtr, $require_unslash = false ) {

		// First we check if it is being casted to a safe value.
		if ( $this->is_safe_casted( $stackPtr ) ) {
			return true;
		}

		// If this isn't within a function call, we know already that it's not safe.
		if ( ! isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			if ( $require_unslash ) {
				$this->add_unslash_error( $stackPtr );
			}

			return false;
		}

		// Get the function that it's in.
		$nested_parenthesis = $this->tokens[ $stackPtr ]['nested_parenthesis'];
		$nested_openers     = array_keys( $nested_parenthesis );
		$function_opener    = array_pop( $nested_openers );
		$functionPtr        = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $function_opener - 1 ), null, true, null, true );

		// If it is just being unset, the value isn't used at all, so it's safe.
		if ( \T_UNSET === $this->tokens[ $functionPtr ]['code'] ) {
			return true;
		}

		$valid_functions  = $this->sanitizingFunctions;
		$valid_functions += $this->unslashingSanitizingFunctions;
		$valid_functions += $this->unslashingFunctions;
		$valid_functions += $this->arrayWalkingFunctions;

		$functionPtr = $this->is_in_function_call( $stackPtr, $valid_functions );

		// If this isn't a call to one of the valid functions, it sure isn't a sanitizing function.
		if ( false === $functionPtr ) {
			if ( true === $require_unslash ) {
				$this->add_unslash_error( $stackPtr );
			}

			return false;
		}

		$functionName = $this->tokens[ $functionPtr ]['content'];

		// Check if an unslashing function is being used.
		if ( isset( $this->unslashingFunctions[ $functionName ] ) ) {

			$is_unslashed = true;

			// Remove the unslashing functions.
			$valid_functions = array_diff_key( $valid_functions, $this->unslashingFunctions );

			// Check is any of the remaining (sanitizing) functions is used.
			$higherFunctionPtr = $this->is_in_function_call( $functionPtr, $valid_functions );

			// If there is no other valid function being used, this value is unsanitized.
			if ( false === $higherFunctionPtr ) {
				return false;
			}

			$functionPtr  = $higherFunctionPtr;
			$functionName = $this->tokens[ $functionPtr ]['content'];

		} else {
			$is_unslashed = false;
		}

		// Arrays might be sanitized via an array walking function using a callback.
		if ( isset( $this->arrayWalkingFunctions[ $functionName ] ) ) {

			// Get the callback parameter.
			$callback = PassedParameters::getParameter( $this->phpcsFile, $functionPtr, $this->arrayWalkingFunctions[ $functionName ] );

			if ( ! empty( $callback ) ) {
				/*
				 * If this is a function callback (not a method callback array) and we're able
				 * to resolve the function name, do so.
				 */
				$first_non_empty = $this->phpcsFile->findNext(
					Tokens::$emptyTokens,
					$callback['start'],
					( $callback['end'] + 1 ),
					true
				);

				if ( false !== $first_non_empty && \T_CONSTANT_ENCAPSED_STRING === $this->tokens[ $first_non_empty ]['code'] ) {
					$functionName = TextStrings::stripQuotes( $this->tokens[ $first_non_empty ]['content'] );
				}
			}
		}

		// If slashing is required, give an error.
		if ( ! $is_unslashed && $require_unslash && ! isset( $this->unslashingSanitizingFunctions[ $functionName ] ) ) {
			$this->add_unslash_error( $stackPtr );
		}

		// Check if this is a sanitizing function.
		if ( isset( $this->sanitizingFunctions[ $functionName ] ) || isset( $this->unslashingSanitizingFunctions[ $functionName ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add an error for missing use of unslashing.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 */
	public function add_unslash_error( $stackPtr ) {

		$this->phpcsFile->addError(
			'%s data not unslashed before sanitization. Use wp_unslash() or similar',
			$stackPtr,
			'MissingUnslash',
			array( $this->tokens[ $stackPtr ]['content'] )
		);
	}

	/**
	 * Check if the existence of a variable is validated with isset(), empty(), array_key_exists()
	 * or key_exists().
	 *
	 * When $in_condition_only is false, (which is the default), this is considered
	 * valid:
	 *
	 * ```php
	 * if ( isset( $var ) ) {
	 *     // Do stuff, like maybe return or exit (but could be anything)
	 * }
	 *
	 * foo( $var );
	 * ```
	 *
	 * When it is true, that would be invalid, the use of the variable must be within
	 * the scope of the validating condition, like this:
	 *
	 * ```php
	 * if ( isset( $var ) ) {
	 *     foo( $var );
	 * }
	 * ```
	 *
	 * @since 0.5.0
	 * @since 2.1.0 Now recognizes array_key_exists() and key_exists() as validation functions.
	 * @since 2.1.0 Stricter check on whether the correct variable and the correct
	 *              array keys are being validated.
	 *
	 * @param int          $stackPtr          The index of this token in the stack.
	 * @param array|string $array_keys        An array key to check for ("bar" in $foo['bar'])
	 *                                        or an array of keys for multi-level array access.
	 * @param bool         $in_condition_only Whether to require that this use of the
	 *                                        variable occur within the scope of the
	 *                                        validating condition, or just in the same
	 *                                        scope as it (default).
	 *
	 * @return bool Whether the var is validated.
	 */
	protected function is_validated( $stackPtr, $array_keys = array(), $in_condition_only = false ) {

		if ( $in_condition_only ) {
			/*
			 * This is a stricter check, requiring the variable to be used only
			 * within the validation condition.
			 */

			// If there are no conditions, there's no validation.
			if ( empty( $this->tokens[ $stackPtr ]['conditions'] ) ) {
				return false;
			}

			$conditions = $this->tokens[ $stackPtr ]['conditions'];
			end( $conditions ); // Get closest condition.
			$conditionPtr = key( $conditions );
			$condition    = $this->tokens[ $conditionPtr ];

			if ( ! isset( $condition['parenthesis_opener'] ) ) {
				// Live coding or parse error.
				return false;
			}

			$scope_start = $condition['parenthesis_opener'];
			$scope_end   = $condition['parenthesis_closer'];

		} else {
			/*
			 * We are are more loose, requiring only that the variable be validated
			 * in the same function/file scope as it is used.
			 */

			$scope_start = 0;

			// Check if we are in a function.
			$function = $this->phpcsFile->getCondition( $stackPtr, \T_FUNCTION );

			// If so, we check only within the function, otherwise the whole file.
			if ( false !== $function ) {
				$scope_start = $this->tokens[ $function ]['scope_opener'];
			} else {
				// Check if we are in a closure.
				$closure = $this->phpcsFile->getCondition( $stackPtr, \T_CLOSURE );

				// If so, we check only within the closure.
				if ( false !== $closure ) {
					$scope_start = $this->tokens[ $closure ]['scope_opener'];
				}
			}

			$scope_end = $stackPtr;
		}

		if ( ! empty( $array_keys ) && ! is_array( $array_keys ) ) {
			$array_keys = (array) $array_keys;
		}

		$bare_array_keys = array_map( array( 'PHPCSUtils\Utils\TextStrings', 'stripQuotes' ), $array_keys );
		$targets         = array(
			\T_ISSET          => 'construct',
			\T_EMPTY          => 'construct',
			\T_UNSET          => 'construct',
			\T_STRING         => 'function_call',
			\T_COALESCE       => 'coalesce',
			\T_COALESCE_EQUAL => 'coalesce',
		);

		// phpcs:ignore Generic.CodeAnalysis.JumbledIncrementer.Found -- On purpose, see below.
		for ( $i = ( $scope_start + 1 ); $i < $scope_end; $i++ ) {

			if ( isset( $targets[ $this->tokens[ $i ]['code'] ] ) === false ) {
				continue;
			}

			switch ( $targets[ $this->tokens[ $i ]['code'] ] ) {
				case 'construct':
					$issetOpener = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), null, true, null, true );
					if ( false === $issetOpener || \T_OPEN_PARENTHESIS !== $this->tokens[ $issetOpener ]['code'] ) {
						// Parse error or live coding.
						continue 2;
					}

					$issetCloser = $this->tokens[ $issetOpener ]['parenthesis_closer'];

					// Look for this variable. We purposely stomp $i from the parent loop.
					for ( $i = ( $issetOpener + 1 ); $i < $issetCloser; $i++ ) {

						if ( \T_VARIABLE !== $this->tokens[ $i ]['code'] ) {
							continue;
						}

						if ( $this->tokens[ $stackPtr ]['content'] !== $this->tokens[ $i ]['content'] ) {
							continue;
						}

						// If we're checking for specific array keys (ex: 'hello' in
						// $_POST['hello']), that must match too. Quote-style, however, doesn't matter.
						if ( ! empty( $bare_array_keys ) ) {
							$found_keys = VariableHelper::get_array_access_keys( $this->phpcsFile, $i );
							$found_keys = array_map( array( 'PHPCSUtils\Utils\TextStrings', 'stripQuotes' ), $found_keys );
							$diff       = array_diff_assoc( $bare_array_keys, $found_keys );
							if ( ! empty( $diff ) ) {
								continue;
							}
						}

						return true;
					}

					break;

				case 'function_call':
					// Only check calls to array_key_exists() and key_exists().
					if ( 'array_key_exists' !== $this->tokens[ $i ]['content']
						&& 'key_exists' !== $this->tokens[ $i ]['content']
					) {
						continue 2;
					}

					$next_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), null, true, null, true );
					if ( false === $next_non_empty || \T_OPEN_PARENTHESIS !== $this->tokens[ $next_non_empty ]['code'] ) {
						// Not a function call.
						continue 2;
					}

					if ( $this->is_class_object_call( $i ) === true ) {
						// Method call.
						continue 2;
					}

					if ( $this->is_token_namespaced( $i ) === true ) {
						// Namespaced function call.
						continue 2;
					}

					$params = PassedParameters::getParameters( $this->phpcsFile, $i );
					if ( count( $params ) < 2 ) {
						continue 2;
					}

					$param2_first_token = $this->phpcsFile->findNext( Tokens::$emptyTokens, $params[2]['start'], ( $params[2]['end'] + 1 ), true );
					if ( false === $param2_first_token
						|| \T_VARIABLE !== $this->tokens[ $param2_first_token ]['code']
						|| $this->tokens[ $param2_first_token ]['content'] !== $this->tokens[ $stackPtr ]['content']
					) {
						continue 2;
					}

					if ( ! empty( $bare_array_keys ) ) {
						// Prevent the original array from being altered.
						$bare_keys = $bare_array_keys;
						$last_key  = array_pop( $bare_keys );

						/*
						 * For multi-level array access, the complete set of keys could be split between
						 * the first and the second parameter, but could also be completely in the second
						 * parameter, so we need to check both options.
						 */

						$found_keys = VariableHelper::get_array_access_keys( $this->phpcsFile, $param2_first_token );
						$found_keys = array_map( array( 'PHPCSUtils\Utils\TextStrings', 'stripQuotes' ), $found_keys );

						// First try matching the complete set against the second parameter.
						$diff = array_diff_assoc( $bare_array_keys, $found_keys );
						if ( empty( $diff ) ) {
							return true;
						}

						// If that failed, try getting an exact match for the subset against the
						// second parameter and the last key against the first.
						if ( $bare_keys === $found_keys && TextStrings::stripQuotes( $params[1]['raw'] ) === $last_key ) {
							return true;
						}

						// Didn't find the correct array keys.
						continue 2;
					}

					return true;

				case 'coalesce':
					$prev = $i;
					do {
						$prev = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $prev - 1 ), null, true, null, true );
						// Skip over array keys, like `$_GET['key']['subkey']`.
						if ( \T_CLOSE_SQUARE_BRACKET === $this->tokens[ $prev ]['code'] ) {
							$prev = $this->tokens[ $prev ]['bracket_opener'];
							continue;
						}

						break;
					} while ( $prev >= ( $scope_start + 1 ) );

					// We should now have reached the variable.
					if ( \T_VARIABLE !== $this->tokens[ $prev ]['code'] ) {
						continue 2;
					}

					if ( $this->tokens[ $prev ]['content'] !== $this->tokens[ $stackPtr ]['content'] ) {
						continue 2;
					}

					if ( ! empty( $bare_array_keys ) ) {
						$found_keys = VariableHelper::get_array_access_keys( $this->phpcsFile, $prev );
						$found_keys = array_map( array( 'PHPCSUtils\Utils\TextStrings', 'stripQuotes' ), $found_keys );
						$diff       = array_diff_assoc( $bare_array_keys, $found_keys );
						if ( ! empty( $diff ) ) {
							continue 2;
						}
					}

					// Right variable, correct key.
					return true;
			}
		}

		return false;
	}

	/**
	 * Check if a token is inside of an array-value comparison function.
	 *
	 * @since 2.1.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool Whether the token is (part of) a parameter to an
	 *              array-value comparison function.
	 */
	protected function is_in_array_comparison( $stackPtr ) {
		$function_ptr = $this->is_in_function_call( $stackPtr, $this->arrayCompareFunctions, true, true );
		if ( false === $function_ptr ) {
			return false;
		}

		$function_name = $this->tokens[ $function_ptr ]['content'];
		if ( true === $this->arrayCompareFunctions[ $function_name ] ) {
			return true;
		}

		if ( PassedParameters::getParameterCount( $this->phpcsFile, $function_ptr ) >= $this->arrayCompareFunctions[ $function_name ] ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks whether this is a call to a $wpdb method that we want to sniff.
	 *
	 * If available in the child class, the $methodPtr, $i and $end properties are
	 * automatically set to correspond to the start and end of the method call.
	 * The $i property is also set if this is not a method call but rather the
	 * use of a $wpdb property.
	 *
	 * @since 0.8.0
	 * @since 0.9.0  The return value is now always boolean. The $end and $i member
	 *               vars are automatically updated.
	 * @since 0.14.0 Moved this method from the `PreparedSQL` sniff to the base WP sniff.
	 *
	 * {@internal This method should probably be refactored.}}
	 *
	 * @param int   $stackPtr       The index of the $wpdb variable.
	 * @param array $target_methods Array of methods. Key(s) should be method name.
	 *
	 * @return bool Whether this is a $wpdb method call.
	 */
	protected function is_wpdb_method_call( $stackPtr, $target_methods ) {

		// Check for wpdb.
		if ( ( \T_VARIABLE === $this->tokens[ $stackPtr ]['code'] && '$wpdb' !== $this->tokens[ $stackPtr ]['content'] )
			|| ( \T_STRING === $this->tokens[ $stackPtr ]['code'] && 'wpdb' !== $this->tokens[ $stackPtr ]['content'] )
		) {
			return false;
		}

		// Check that this is a method call.
		$is_object_call = $this->phpcsFile->findNext(
			array( \T_OBJECT_OPERATOR, \T_DOUBLE_COLON ),
			( $stackPtr + 1 ),
			null,
			false,
			null,
			true
		);
		if ( false === $is_object_call ) {
			return false;
		}

		$methodPtr = $this->phpcsFile->findNext( \T_WHITESPACE, ( $is_object_call + 1 ), null, true, null, true );
		if ( false === $methodPtr ) {
			return false;
		}

		if ( \T_STRING === $this->tokens[ $methodPtr ]['code'] && property_exists( $this, 'methodPtr' ) ) {
			$this->methodPtr = $methodPtr;
		}

		// Find the opening parenthesis.
		$opening_paren = $this->phpcsFile->findNext( \T_WHITESPACE, ( $methodPtr + 1 ), null, true, null, true );

		if ( false === $opening_paren ) {
			return false;
		}

		if ( property_exists( $this, 'i' ) ) {
			$this->i = $opening_paren;
		}

		if ( \T_OPEN_PARENTHESIS !== $this->tokens[ $opening_paren ]['code']
			|| ! isset( $this->tokens[ $opening_paren ]['parenthesis_closer'] )
		) {
			return false;
		}

		// Check that this is one of the methods that we are interested in.
		if ( ! isset( $target_methods[ $this->tokens[ $methodPtr ]['content'] ] ) ) {
			return false;
		}

		// Find the end of the first parameter.
		$end = $this->phpcsFile->findEndOfStatement( $opening_paren + 1 );

		if ( \T_COMMA !== $this->tokens[ $end ]['code'] ) {
			++$end;
		}

		if ( property_exists( $this, 'end' ) ) {
			$this->end = $end;
		}

		return true;
	}

	/**
	 * Determine whether an arbitrary T_STRING token is the use of a global constant.
	 *
	 * @since 1.0.0
	 *
	 * @param int $stackPtr The position of the function call token.
	 *
	 * @return bool
	 */
	public function is_use_of_global_constant( $stackPtr ) {
		// Check for the existence of the token.
		if ( ! isset( $this->tokens[ $stackPtr ] ) ) {
			return false;
		}

		// Is this one of the tokens this function handles ?
		if ( \T_STRING !== $this->tokens[ $stackPtr ]['code'] ) {
			return false;
		}

		$next = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );
		if ( false !== $next
			&& ( \T_OPEN_PARENTHESIS === $this->tokens[ $next ]['code']
				|| \T_DOUBLE_COLON === $this->tokens[ $next ]['code'] )
		) {
			// Function call or declaration.
			return false;
		}

		// Array of tokens which if found preceding the $stackPtr indicate that a T_STRING is not a global constant.
		$tokens_to_ignore = array(
			'T_NAMESPACE'       => true,
			'T_USE'             => true,
			'T_CLASS'           => true,
			'T_TRAIT'           => true,
			'T_INTERFACE'       => true,
			'T_EXTENDS'         => true,
			'T_IMPLEMENTS'      => true,
			'T_NEW'             => true,
			'T_FUNCTION'        => true,
			'T_DOUBLE_COLON'    => true,
			'T_OBJECT_OPERATOR' => true,
			'T_INSTANCEOF'      => true,
			'T_INSTEADOF'       => true,
			'T_GOTO'            => true,
			'T_AS'              => true,
			'T_PUBLIC'          => true,
			'T_PROTECTED'       => true,
			'T_PRIVATE'         => true,
		);

		$prev = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true );
		if ( false !== $prev
			&& isset( $tokens_to_ignore[ $this->tokens[ $prev ]['type'] ] )
		) {
			// Not the use of a constant.
			return false;
		}

		if ( $this->is_token_namespaced( $stackPtr ) === true ) {
			// Namespaced constant of the same name.
			return false;
		}

		if ( false !== $prev
			&& \T_CONST === $this->tokens[ $prev ]['code']
			&& Scopes::isOOConstant( $this->phpcsFile, $prev )
		) {
			// Class constant declaration of the same name.
			return false;
		}

		/*
		 * Deal with a number of variations of use statements.
		 */
		for ( $i = $stackPtr; $i > 0; $i-- ) {
			if ( $this->tokens[ $i ]['line'] !== $this->tokens[ $stackPtr ]['line'] ) {
				break;
			}
		}

		$firstOnLine = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), null, true );
		if ( false !== $firstOnLine && \T_USE === $this->tokens[ $firstOnLine ]['code'] ) {
			$nextOnLine = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $firstOnLine + 1 ), null, true );
			if ( false !== $nextOnLine ) {
				if ( \T_STRING === $this->tokens[ $nextOnLine ]['code']
					&& 'const' === $this->tokens[ $nextOnLine ]['content']
				) {
					$hasNsSep = $this->phpcsFile->findNext( \T_NS_SEPARATOR, ( $nextOnLine + 1 ), $stackPtr );
					if ( false !== $hasNsSep ) {
						// Namespaced const (group) use statement.
						return false;
					}
				} else {
					// Not a const use statement.
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Determine if a variable is in the `as $key => $value` part of a foreach condition.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Moved from the PrefixAllGlobals sniff to the Sniff base class.
	 *
	 * @param int $stackPtr Pointer to the variable.
	 *
	 * @return bool True if it is. False otherwise.
	 */
	protected function is_foreach_as( $stackPtr ) {
		if ( ! isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			return false;
		}

		$nested_parenthesis = $this->tokens[ $stackPtr ]['nested_parenthesis'];
		$close_parenthesis  = end( $nested_parenthesis );
		$open_parenthesis   = key( $nested_parenthesis );
		if ( ! isset( $this->tokens[ $close_parenthesis ]['parenthesis_owner'] ) ) {
			return false;
		}

		if ( \T_FOREACH !== $this->tokens[ $this->tokens[ $close_parenthesis ]['parenthesis_owner'] ]['code'] ) {
			return false;
		}

		$as_ptr = $this->phpcsFile->findNext( \T_AS, ( $open_parenthesis + 1 ), $close_parenthesis );
		if ( false === $as_ptr ) {
			// Should never happen.
			return false;
		}

		return ( $stackPtr > $as_ptr );
	}

	/**
	 * Get a list of the token pointers to the variables being assigned to in a list statement.
	 *
	 * @internal No need to take special measures for nested lists. Nested or not,
	 * each list part can only contain one variable being written to.
	 *
	 * @since 2.2.0
	 *
	 * @param int   $stackPtr        The position of the T_LIST or T_OPEN_SHORT_ARRAY
	 *                               token in the stack.
	 * @param array $list_open_close Optional. Array containing the token pointers to
	 *                               the list opener and closer.
	 *
	 * @return array Array with the stack pointers to the variables or an empty
	 *               array when not a (short) list.
	 */
	protected function get_list_variables( $stackPtr, $list_open_close = array() ) {
		if ( \T_LIST !== $this->tokens[ $stackPtr ]['code']
			&& \T_OPEN_SHORT_ARRAY !== $this->tokens[ $stackPtr ]['code']
		) {
			return array();
		}

		if ( empty( $list_open_close ) ) {
			$list_open_close = Lists::getOpenClose( $this->phpcsFile, $stackPtr );
			if ( false === $list_open_close ) {
				// Not a (short) list.
				return array();
			}
		}

		$var_pointers = array();
		$current      = $list_open_close['opener'];
		$closer       = $list_open_close['closer'];
		$last         = false;
		do {
			++$current;
			$next_comma = $this->phpcsFile->findNext( \T_COMMA, $current, $closer );
			if ( false === $next_comma ) {
				$next_comma = $closer;
				$last       = true;
			}

			// Skip over the "key" part in keyed lists.
			$arrow = $this->phpcsFile->findNext( \T_DOUBLE_ARROW, $current, $next_comma );
			if ( false !== $arrow ) {
				$current = ( $arrow + 1 );
			}

			/*
			 * Each list item can only have one variable to which an assignment is being made.
			 * This can be an array with a (variable) index, but that doesn't matter, we're only
			 * concerned with the actual variable.
			 */
			$var = $this->phpcsFile->findNext( \T_VARIABLE, $current, $next_comma );
			if ( false !== $var ) {
				// Not an empty list item.
				$var_pointers[] = $var;
			}

			$current = $next_comma;

		} while ( false === $last );

		return $var_pointers;
	}
}
