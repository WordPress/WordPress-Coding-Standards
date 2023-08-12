<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

/**
 * Helper utilities for recognizing WP global variables.
 *
 * @since 3.0.0 The property in this class was previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class WPGlobalVariablesHelper {

	/**
	 * List of global WP variables.
	 *
	 * @since 0.3.0
	 * @since 0.11.0 Changed visibility from public to protected.
	 * @since 0.12.0 Renamed from `$globals` to `$wp_globals` to be more descriptive.
	 * @since 0.12.0 Moved to the `Sniff` class from the WordPress.Variables.GlobalVariables sniff.
	 * @since 3.0.0  - Moved from the Sniff class to this class.
	 *               - The property visibility has changed from `protected` to `private static`.
	 *                 Use the `get_names()` method for access or use the `is_wp_global()` method
	 *                 to check if a name is on the list.
	 *
	 * @var array<string, bool> The key is the name of a WP global variable, the value is irrelevant.
	 */
	private static $wp_globals = array(
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
	 * Retrieve a list with the names of global WP variables.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, bool> Array with the variables names as keys. The value is irrelevant.
	 */
	public static function get_names() {
		return self::$wp_globals;
	}

	/**
	 * Verify if a given variable name is the name of a WP global variable.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name The full variable name with or without leading dollar sign.
	 *                     This allows for passing an array key variable name, such as
	 *                     `'_GET'` retrieved from `$GLOBALS['_GET']`.
	 *                     > Note: when passing an array key, string quotes are expected
	 *                     to have been stripped already.
	 *
	 * @return bool
	 */
	public static function is_wp_global( $name ) {
		if ( strpos( $name, '$' ) === 0 ) {
			$name = substr( $name, 1 );
		}

		return isset( self::$wp_globals[ $name ] );
	}
}
