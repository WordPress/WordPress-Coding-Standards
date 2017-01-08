<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Restricts the use of various deprecated WordPress functions and suggests alternatives.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0
 */
class WordPress_Sniffs_WP_DeprecatedFunctionsSniff extends WordPress_AbstractFunctionRestrictionsSniff {

	/**
	 * Minimum WordPress version.
	 *
	 * This sniff will throw an error when usage of deprecated functions is
	 * detected if the function was deprecated before the minimum supported
	 * WP version; a warning otherwise.
	 * By default, it is set to presume that a project will support the current
	 * WP version and up to three releases before.
	 * This variable allows changing the minimum supported WP version used by
	 * this sniff by setting a property in a custom phpcs.xml ruleset.
	 *
	 * Example usage:
	 * <rule ref="WordPress.WP.WP_DeprecatedFunctions">
	 *  <properties>
	 *   <property name="minimum_supported_version" value="4.3"/>
	 *  </properties>
	 * </rule>
	 *
	 * @var string WordPress versions.
	 */
	public $minimum_supported_version = 4.4;

	/**
	 * List of deprecated functions with alternative when available.
	 *
	 * To be updated after every major release.
	 * Last updated for WordPress 4.7.
	 *
	 * @var array
	 */
	private $deprecated_functions = array(
		'the_category_id' => array(
			'alt'     => 'get_the_category()',
			'version' => '0.71',
		),
		'the_category_head' => array(
			'alt'     => 'get_the_category_by_ID()',
			'version' => '0.71',
		),

		'permalink_link' => array(
			'alt'     => 'the_permalink()',
			'version' => '1.2',
		),

		'start_wp' => array(
			'alt'     => 'the Loop',
			'version' => '1.5',
		),
		'get_postdata' => array(
			'alt'     => 'get_post()',
			'version' => '1.5.1',
		),

		'previous_post' => array(
			'alt'     => 'previous_post_link()',
			'version' => '2.0',
		),
		'next_post' => array(
			'alt'     => 'next_post_link()',
			'version' => '2.0',
		),
		'user_can_create_post' => array(
			'alt'     => 'current_user_can()',
			'version' => '2.0',
		),
		'user_can_create_draft' => array(
			'alt'     => 'current_user_can()',
			'version' => '2.0',
		),
		'user_can_edit_post' => array(
			'alt'     => 'current_user_can()',
			'version' => '2.0',
		),
		'user_can_delete_post' => array(
			'alt'     => 'current_user_can()',
			'version' => '2.0',
		),
		'user_can_set_post_date' => array(
			'alt'     => 'current_user_can()',
			'version' => '2.0',
		),
		'user_can_edit_post_comments' => array(
			'alt'     => 'current_user_can()',
			'version' => '2.0',
		),
		'user_can_delete_post_comments' => array(
			'alt'     => 'current_user_can()',
			'version' => '2.0',
		),
		'user_can_edit_user' => array(
			'alt'     => 'current_user_can()',
			'version' => '2.0',
		),
		'create_user' => array(
			'alt'     => 'wp_create_user()',
			'version' => '2.0',
		),

		'get_linksbyname' => array(
			'alt'     => 'get_bookmarks()',
			'version' => '2.1',
		),
		'wp_get_linksbyname' => array(
			'alt'     => 'wp_list_bookmarks()',
			'version' => '2.1',
		),
		'get_linkobjectsbyname' => array(
			'alt'     => 'get_bookmarks()',
			'version' => '2.1',
		),
		'get_linkobjects' => array(
			'alt'     => 'get_bookmarks()',
			'version' => '2.1',
		),
		'get_linksbyname_withrating' => array(
			'alt'     => 'get_bookmarks()',
			'version' => '2.1',
		),
		'get_links_withrating' => array(
			'alt'     => 'get_bookmarks()',
			'version' => '2.1',
		),
		'get_autotoggle' => array(
			'alt'     => '',
			'version' => '2.1',
		),
		'list_cats' => array(
			'alt'     => 'wp_list_categories',
			'version' => '2.1',
		),
		'wp_list_cats' => array(
			'alt'     => 'wp_list_categories',
			'version' => '2.1',
		),
		'dropdown_cats' => array(
			'alt'     => 'wp_dropdown_categories()',
			'version' => '2.1',
		),
		'list_authors' => array(
			'alt'     => 'wp_list_authors()',
			'version' => '2.1',
		),
		'wp_get_post_cats' => array(
			'alt'     => 'wp_get_post_categories()',
			'version' => '2.1',
		),
		'wp_set_post_cats' => array(
			'alt'     => 'wp_set_post_categories()',
			'version' => '2.1',
		),
		'get_archives' => array(
			'alt'     => 'wp_get_archives',
			'version' => '2.1',
		),
		'get_author_link' => array(
			'alt'     => 'get_author_posts_url()',
			'version' => '2.1',
		),
		'link_pages' => array(
			'alt'     => 'wp_link_pages()',
			'version' => '2.1',
		),
		'get_settings' => array(
			'alt'     => 'get_option()',
			'version' => '2.1',
		),
		'wp_get_links' => array(
			'alt'     => 'wp_list_bookmarks()',
			'version' => '2.1',
		),
		'get_links' => array(
			'alt'     => 'get_bookmarks()',
			'version' => '2.1',
		),
		'get_links_list' => array(
			'alt'     => 'wp_list_bookmarks()',
			'version' => '2.1',
		),
		'links_popup_script' => array(
			'alt'     => '',
			'version' => '2.1',
		),
		'get_linkrating' => array(
			'alt'     => 'sanitize_bookmark_field()',
			'version' => '2.1',
		),
		'get_linkcatname' => array(
			'alt'     => 'get_category()',
			'version' => '2.1',
		),
		'get_link' => array(
			'alt'     => 'get_bookmark()',
			'version' => '2.1',
		),
		'tinymce_include' => array(
			'alt'     => 'wp_tiny_mce()',
			'version' => '2.1',
		),

		'comments_rss' => array(
			'alt'     => 'get_post_comments_feed_link()',
			'version' => '2.2',
		),

		'permalink_single_rss' => array(
			'alt'     => 'permalink_rss()',
			'version' => '2.3',
		),

		'comments_rss_link' => array(
			'alt'     => 'post_comments_feed_link()',
			'version' => '2.5',
		),
		'get_category_rss_link' => array(
			'alt'     => 'get_category_feed_link()',
			'version' => '2.5',
		),
		'get_author_rss_link' => array(
			'alt'     => 'get_author_feed_link()',
			'version' => '2.5',
		),
		'get_the_attachment_link' => array(
			'alt'     => 'wp_get_attachment_link()',
			'version' => '2.5',
		),
		'get_attachment_icon_src' => array(
			'alt'     => 'wp_get_attachment_image_src()',
			'version' => '2.5',
		),
		'get_attachment_icon' => array(
			'alt'     => 'wp_get_attachment_image()',
			'version' => '2.5',
		),
		'get_attachment_innerHTML' => array(
			'alt'     => 'wp_get_attachment_image()',
			'version' => '2.5',
		),
		'documentation_link' => array(
			'alt'     => '',
			'version' => '2.5',
		),
		'gzip_compression' => array(
			'alt'     => '',
			'version' => '2.5',
		),
		'wp_setcookie' => array(
			'alt'     => 'wp_set_auth_cookie()',
			'version' => '2.5',
		),
		'wp_get_cookie_login' => array(
			'alt'     => '',
			'version' => '2.5',
		),
		'wp_login' => array(
			'alt'     => 'wp_signon()',
			'version' => '2.5',
		),

		'dropdown_categories' => array(
			'alt'     => 'wp_category_checklist()',
			'version' => '2.6',
		),
		'dropdown_link_categories' => array(
			'alt'     => 'wp_link_category_checklist()',
			'version' => '2.6',
		),

		'get_commentdata' => array(
			'alt'     => 'get_comment()',
			'version' => '2.7',
		),
		// This is a method i.e. WP_Filesystem_Base::find_base_dir() See #731.
		'find_base_dir' => array(
			'alt'     => 'WP_Filesystem::abspath()',
			'version' => '2.7',
		),
		// This is a method i.e. WP_Filesystem_Base::get_base_dir() See #731.
		'get_base_dir' => array(
			'alt'     => 'WP_Filesystem::abspath()',
			'version' => '2.7',
		),

		'get_catname' => array(
			'alt'     => 'get_cat_name()',
			'version' => '2.8',
		),
		'get_category_children' => array(
			'alt'     => 'get_term_children',
			'version' => '2.8',
		),
		'get_the_author_description' => array(
			'alt'     => "get_the_author_meta( 'description' )",
			'version' => '2.8',
		),
		'the_author_description' => array(
			'alt'     => 'the_author_meta(\'description\')',
			'version' => '2.8',
		),
		'get_the_author_login' => array(
			'alt'     => 'the_author_meta(\'login\')',
			'version' => '2.8',
		),
		'get_the_author_firstname' => array(
			'alt'     => 'get_the_author_meta(\'first_name\')',
			'version' => '2.8',
		),
		'the_author_firstname' => array(
			'alt'     => 'the_author_meta(\'first_name\')',
			'version' => '2.8',
		),
		'get_the_author_lastname' => array(
			'alt'     => 'get_the_author_meta(\'last_name\')',
			'version' => '2.8',
		),
		'the_author_lastname' => array(
			'alt'     => 'the_author_meta(\'last_name\')',
			'version' => '2.8',
		),
		'get_the_author_nickname' => array(
			'alt'     => 'get_the_author_meta(\'nickname\')',
			'version' => '2.8',
		),
		'the_author_nickname' => array(
			'alt'     => 'the_author_meta(\'nickname\')',
			'version' => '2.8',
		),
		'get_the_author_email' => array(
			'alt'     => 'get_the_author_meta(\'email\')',
			'version' => '2.8',
		),
		'the_author_email' => array(
			'alt'     => 'the_author_meta(\'email\')',
			'version' => '2.8',
		),
		'get_the_author_icq' => array(
			'alt'     => 'get_the_author_meta(\'icq\')',
			'version' => '2.8',
		),
		'the_author_icq' => array(
			'alt'     => 'the_author_meta(\'icq\')',
			'version' => '2.8',
		),
		'get_the_author_yim' => array(
			'alt'     => 'get_the_author_meta(\'yim\')',
			'version' => '2.8',
		),
		'the_author_yim' => array(
			'alt'     => 'the_author_meta(\'yim\')',
			'version' => '2.8',
		),
		'get_the_author_msn' => array(
			'alt'     => 'get_the_author_meta(\'msn\')',
			'version' => '2.8',
		),
		'the_author_msn' => array(
			'alt'     => 'the_author_meta(\'msn\')',
			'version' => '2.8',
		),
		'get_the_author_aim' => array(
			'alt'     => 'get_the_author_meta(\'aim\')',
			'version' => '2.8',
		),
		'the_author_aim' => array(
			'alt'     => 'the_author_meta(\'aim\')',
			'version' => '2.8',
		),
		'get_author_name' => array(
			'alt'     => 'get_the_author_meta(\'display_name\')',
			'version' => '2.8',
		),
		'get_the_author_url' => array(
			'alt'     => 'get_the_author_meta(\'url\')',
			'version' => '2.8',
		),
		'the_author_url' => array(
			'alt'     => 'the_author_meta(\'url\')',
			'version' => '2.8',
		),
		'get_the_author_ID' => array(
			'alt'     => 'get_the_author_meta(\'ID\')',
			'version' => '2.8',
		),
		'the_author_ID' => array(
			'alt'     => 'the_author_meta(\'ID\')',
			'version' => '2.8',
		),
		'__ngettext' => array(
			'alt'     => '_n_noop()',
			'version' => '2.8',
		),
		'__ngettext_noop' => array(
			'alt'     => '_n_noop()',
			'version' => '2.8',
		),
		'sanitize_url' => array(
			'alt'     => 'esc_url()',
			'version' => '2.8',
		),
		'js_escape' => array(
			'alt'     => 'esc_js()',
			'version' => '2.8',
		),
		'wp_specialchars' => array(
			'alt'     => 'esc_html()',
			'version' => '2.8',
		),
		'attribute_escape' => array(
			'alt'     => 'esc_attr()',
			'version' => '2.8',
		),
		'register_sidebar_widget' => array(
			'alt'     => 'wp_register_sidebar_widget()',
			'version' => '2.8',
		),
		'unregister_sidebar_widget' => array(
			'alt'     => 'wp_unregister_sidebar_widget()',
			'version' => '2.8',
		),
		'register_widget_control' => array(
			'alt'     => 'wp_register_widget_control()',
			'version' => '2.8',
		),
		'unregister_widget_control' => array(
			'alt'     => 'wp_unregister_widget_control()',
			'version' => '2.8',
		),

		'the_content_rss' => array(
			'alt'     => 'the_content_feed()',
			'version' => '2.9',
		),
		'make_url_footnote' => array(
			'alt'     => '',
			'version' => '2.9',
		),
		'_c' => array(
			'alt'     => '_x()',
			'version' => '2.9',
		),

		'translate_with_context' => array(
			'alt'     => '_x()',
			'version' => '3.0',
		),
		'nc' => array(
			'alt'     => 'nx()',
			'version' => '3.0',
		),
		'get_alloptions' => array(
			'alt'     => 'wp_load_alloptions()',
			'version' => '3.0',
		),
		'clean_url' => array(
			'alt'     => 'esc_url()',
			'version' => '3.0',
		),
		'delete_usermeta' => array(
			'alt'     => 'delete_user_meta()',
			'version' => '3.0',
		),
		'get_usermeta' => array(
			'alt'     => 'get_user_meta()',
			'version' => '3.0',
		),
		'update_usermeta' => array(
			'alt'     => 'update_user_meta()',
			'version' => '3.0',
		),
		'automatic_feed_links' => array(
			'alt'     => 'add_theme_support( \'automatic-feed-links\' )',
			'version' => '3.0',
		),
		'get_profile' => array(
			'alt'     => 'get_the_author_meta()',
			'version' => '3.0',
		),
		'get_usernumposts' => array(
			'alt'     => 'count_user_posts()',
			'version' => '3.0',
		),
		'funky_javascript_callback' => array(
			'alt'     => '',
			'version' => '3.0',
		),
		'funky_javascript_fix' => array(
			'alt'     => '',
			'version' => '3.0',
		),
		'is_taxonomy' => array(
			'alt'     => 'taxonomy_exists()',
			'version' => '3.0',
		),
		'is_term' => array(
			'alt'     => 'term_exists()',
			'version' => '3.0',
		),
		'wp_dropdown_cats' => array(
			'alt'     => 'wp_dropdown_categories()',
			'version' => '3.0',
		),
		'add_option_update_handler' => array(
			'alt'     => 'register_setting()',
			'version' => '3.0',
		),
		'remove_option_update_handler' => array(
			'alt'     => 'unregister_setting()',
			'version' => '3.0',
		),
		'codepress_get_lang' => array(
			'alt'     => '',
			'version' => '3.0',
		),
		'codepress_footer_js' => array(
			'alt'     => '',
			'version' => '3.0',
		),
		'use_codepress' => array(
			'alt'     => '',
			'version' => '3.0',
		),
		'wp_shrink_dimensions' => array(
			'alt'     => 'wp_constrain_dimensions()',
			'version' => '3.0',
		),

		'is_plugin_page' => array(
			'alt'     => '$plugin_page and/or get_plugin_page_hookname() hooks',
			'version' => '3.1',
		),
		'update_category_cache' => array(
			'alt'     => '',
			'version' => '3.1',
		),
		'get_users_of_blog' => array(
			'alt'     => 'get_users()',
			'version' => '3.1',
		),
		'get_author_user_ids' => array(
			'alt'     => '',
			'version' => '3.1',
		),
		'get_editable_authors' => array(
			'alt'     => '',
			'version' => '3.1',
		),
		'get_editable_user_ids' => array(
			'alt'     => '',
			'version' => '3.1',
		),
		'get_nonauthor_user_ids' => array(
			'alt'     => '',
			'version' => '3.1',
		),
		'WP_User_Search' => array(
			'alt'     => 'WP_User_Query',
			'version' => '3.1',
		),
		'get_others_unpublished_posts' => array(
			'alt'     => '',
			'version' => '3.1',
		),
		'get_others_drafts' => array(
			'alt'     => '',
			'version' => '3.1',
		),
		'get_others_pending' => array(
			'alt'     => '',
			'version' => '3.1',
		),

		'wp_timezone_supported' => array(
			'alt'     => '',
			'version' => '3.2',
		),
		'wp_dashboard_quick_press' => array(
			'alt'     => '',
			'version' => '3.2',
		),
		'wp_tiny_mce' => array(
			'alt'     => 'wp_editor',
			'version' => '3.2',
		),
		'wp_preload_dialogs' => array(
			'alt'     => 'wp_editor()',
			'version' => '3.2',
		),
		'wp_print_editor_js' => array(
			'alt'     => 'wp_editor()',
			'version' => '3.2',
		),
		'wp_quicktags' => array(
			'alt'     => 'wp_editor()',
			'version' => '3.2',
		),
		'favorite_actions' => array(
			'alt'     => 'WP_Admin_Bar',
			'version' => '3.2',
		),

		'the_editor' => array(
			'alt'     => 'wp_editor',
			'version' => '3.3',
		),
		'get_user_metavalues' => array(
			'alt'     => '',
			'version' => '3.3',
		),
		'sanitize_user_object' => array(
			'alt'     => '',
			'version' => '3.3',
		),
		'get_boundary_post_rel_link' => array(
			'alt'     => '',
			'version' => '3.3',
		),
		'start_post_rel_link' => array(
			'alt'     => '',
			'version' => '3.3',
		),
		'get_index_rel_link' => array(
			'alt'     => '',
			'version' => '3.3',
		),
		'index_rel_link' => array(
			'alt'     => '',
			'version' => '3.3',
		),
		'get_parent_post_rel_link' => array(
			'alt'     => '',
			'version' => '3.3',
		),
		'parent_post_rel_link' => array(
			'alt'     => '',
			'version' => '3.3',
		),
		'wp_admin_bar_dashboard_view_site_menu' => array(
			'alt'     => '',
			'version' => '3.3',
		),
		'is_blog_user' => array(
			'alt'     => 'is_member_of_blog()',
			'version' => '3.3',
		),
		'debug_fopen' => array(
			'alt'     => 'error_log()',
			'version' => '3.3',
		),
		'debug_fwrite' => array(
			'alt'     => 'error_log()',
			'version' => '3.3',
		),
		'debug_fclose' => array(
			'alt'     => 'error_log()',
			'version' => '3.3',
		),
		'screen_layout' => array(
			'alt'     => '$current_screen->render_screen_layout()',
			'version' => '3.3',
		),
		'screen_options' => array(
			'alt'     => '$current_screen->render_per_page_options()',
			'version' => '3.3',
		),
		'screen_meta' => array(
			'alt'     => '$current_screen->render_screen_meta()',
			'version' => '3.3',
		),
		'media_upload_image' => array(
			'alt'     => 'wp_media_upload_handler()',
			'version' => '3.3',
		),
		'media_upload_audio' => array(
			'alt'     => 'wp_media_upload_handler()',
			'version' => '3.3',
		),
		'media_upload_video' => array(
			'alt'     => 'wp_media_upload_handler()',
			'version' => '3.3',
		),
		'media_upload_file' => array(
			'alt'     => 'wp_media_upload_handler()',
			'version' => '3.3',
		),
		'type_url_form_image' => array(
			'alt'     => 'wp_media_insert_url_form( \'image\' )',
			'version' => '3.3',
		),
		'type_url_form_audio' => array(
			'alt'     => 'wp_media_insert_url_form( \'audio\' )',
			'version' => '3.3',
		),
		'type_url_form_video' => array(
			'alt'     => 'wp_media_insert_url_form( \'video\' )',
			'version' => '3.3',
		),
		'type_url_form_file' => array(
			'alt'     => 'wp_media_insert_url_form( \'file\' )',
			'version' => '3.3',
		),
		'add_contextual_help' => array(
			'alt'     => 'get_current_screen()->add_help_tab()',
			'version' => '3.3',
		),

		'get_themes' => array(
			'alt'     => 'wp_get_themes()',
			'version' => '3.4',
		),
		'get_theme' => array(
			'alt'     => 'wp_get_theme()',
			'version' => '3.4',
		),
		'get_current_theme' => array(
			'alt'     => 'wp_get_theme()',
			'version' => '3.4',
		),
		'clean_pre' => array(
			'alt'     => '',
			'version' => '3.4',
		),
		'add_custom_image_header' => array(
			'alt'     => 'add_theme_support( \'custom-header\', $args )',
			'version' => '3.4',
		),
		'remove_custom_image_header' => array(
			'alt'     => 'remove_theme_support( \'custom-header\' )',
			'version' => '3.4',
		),
		'add_custom_background' => array(
			'alt'     => 'add_theme_support( \'custom-background\', $args )',
			'version' => '3.4',
		),
		'remove_custom_background' => array(
			'alt'     => 'remove_theme_support( \'custom-background\' )',
			'version' => '3.4',
		),
		'get_theme_data' => array(
			'alt'     => 'wp_get_theme()',
			'version' => '3.4',
		),
		'update_page_cache' => array(
			'alt'     => 'update_post_cache()',
			'version' => '3.4',
		),
		'clean_page_cache' => array(
			'alt'     => 'clean_post_cache()',
			'version' => '3.4',
		),
		'get_allowed_themes' => array(
			'alt'     => 'wp_get_themes( array( \'allowed\' => true ) )',
			'version' => '3.4',
		),
		'get_broken_themes' => array(
			'alt'     => 'wp_get_themes( array( \'errors\' => true )',
			'version' => '3.4',
		),
		'current_theme_info' => array(
			'alt'     => 'wp_get_theme()',
			'version' => '3.4',
		),
		'wp_explain_nonce' => array(
			'alt'     => 'wp_nonce_ays',
			'version' => '3.4.1',
		),

		'sticky_class' => array(
			'alt'     => 'post_class()',
			'version' => '3.5',
		),
		'_get_post_ancestors' => array(
			'alt'     => '',
			'version' => '3.5',
		),
		'wp_load_image' => array(
			'alt'     => 'wp_get_image_editor()',
			'version' => '3.5',
		),
		'image_resize' => array(
			'alt'     => 'wp_get_image_editor()',
			'version' => '3.5',
		),
		'wp_get_single_post' => array(
			'alt'     => 'get_post()',
			'version' => '3.5',
		),
		'user_pass_ok' => array(
			'alt'     => 'wp_authenticate()',
			'version' => '3.5',
		),
		'_save_post_hook' => array(
			'alt'     => '',
			'version' => '3.5',
		),
		'gd_edit_image_support' => array(
			'alt'     => 'wp_image_editor_supports',
			'version' => '3.5',
		),
		'_insert_into_post_button' => array(
			'alt'     => '',
			'version' => '3.5',
		),
		'_media_button' => array(
			'alt'     => '',
			'version' => '3.5',
		),
		'get_post_to_edit' => array(
			'alt'     => 'get_post()',
			'version' => '3.5',
		),
		'get_default_page_to_edit' => array(
			'alt'     => 'get_default_post_to_edit()',
			'version' => '3.5',
		),
		'wp_create_thumbnail' => array(
			'alt'     => 'image_resize()',
			'version' => '3.5',
		),

		'get_user_id_from_string' => array(
			'alt'     => 'get_user_by()',
			'version' => '3.6',
		),
		'wp_convert_bytes_to_hr' => array(
			'alt'     => 'size_format()',
			'version' => '3.6',
		),
		'wp_nav_menu_locations_meta_box' => array(
			'alt'     => '',
			'version' => '3.6',
		),

		'the_attachment_links' => array(
			'alt'     => '',
			'version' => '3.7',
		),
		'wp_update_core' => array(
			'alt'     => 'new Core_Upgrader()',
			'version' => '3.7',
		),
		'wp_update_plugin' => array(
			'alt'     => 'new Plugin_Upgrader()',
			'version' => '3.7',
		),
		'wp_update_theme' => array(
			'alt'     => 'new Theme_Upgrader()',
			'version' => '3.7',
		),
		'_search_terms_tidy' => array(
			'alt'     => '',
			'version' => '3.7',
		),
		'get_blogaddress_by_domain' => array(
			'alt'     => '',
			'version' => '3.7',
		),

		'get_screen_icon' => array(
			'alt'     => '',
			'version' => '3.8',
		),
		'screen_icon' => array(
			'alt'     => '',
			'version' => '3.8',
		),
		'wp_dashboard_incoming_links' => array(
			'alt'     => '',
			'version' => '3.8',
		),
		'wp_dashboard_incoming_links_control' => array(
			'alt'     => '',
			'version' => '3.8',
		),
		'wp_dashboard_incoming_links_output' => array(
			'alt'     => '',
			'version' => '3.8',
		),
		'wp_dashboard_plugins' => array(
			'alt'     => '',
			'version' => '3.8',
		),
		'wp_dashboard_primary_control' => array(
			'alt'     => '',
			'version' => '3.8',
		),
		'wp_dashboard_recent_comments_control' => array(
			'alt'     => '',
			'version' => '3.8',
		),
		'wp_dashboard_secondary' => array(
			'alt'     => '',
			'version' => '3.8',
		),
		'wp_dashboard_secondary_control' => array(
			'alt'     => '',
			'version' => '3.8',
		),
		'wp_dashboard_secondary_output' => array(
			'alt'     => '',
			'version' => '3.8',
		),

		'rich_edit_exists' => array(
			'alt'     => '',
			'version' => '3.9',
		),
		'default_topic_count_text' => array(
			'alt'     => '',
			'version' => '3.9',
		),
		'format_to_post' => array(
			'alt'     => '',
			'version' => '3.9',
		),
		'get_current_site_name' => array(
			'alt'     => 'get_current_site()',
			'version' => '3.9',
		),
		'wpmu_current_site' => array(
			'alt'     => '',
			'version' => '3.9',
		),
		'_relocate_children' => array(
			'alt'     => '',
			'version' => '3.9',
		),

		'get_all_category_ids' => array(
			'alt'     => 'get_terms()',
			'version' => '4.0',
		),
		'like_escape' => array(
			'alt'     => 'wpdb::esc_like()',
			'version' => '4.0',
		),
		'url_is_accessable_via_ssl' => array(
			'alt'     => '',
			'version' => '4.0',
		),

		'prepare_control' => array(
			'alt'     => '',
			'version' => '4.1',
		),
		'add_tab' => array(
			'alt'     => '',
			'version' => '4.1',
		),
		'remove_tab' => array(
			'alt'     => '',
			'version' => '4.1',
		),
		'print_tab_image' => array(
			'alt'     => '',
			'version' => '4.1',
		),

		'setup_widget_addition_previews' => array(
			'alt'     => 'customize_dynamic_setting_args',
			'version' => '4.2',
		),
		'prepreview_added_sidebars_widgets' => array(
			'alt'     => 'customize_dynamic_setting_args',
			'version' => '4.2',
		),
		'prepreview_added_widget_instance' => array(
			'alt'     => 'customize_dynamic_setting_args',
			'version' => '4.2',
		),
		'remove_prepreview_filters' => array(
			'alt'     => 'customize_dynamic_setting_args()',
			'version' => '4.2',
		),

		'preview_theme' => array(
			'alt'     => '',
			'version' => '4.3',
		),
		'_preview_theme_template_filter' => array(
			'alt'     => '',
			'version' => '4.3',
		),
		'_preview_theme_stylesheet_filter' => array(
			'alt'     => '',
			'version' => '4.3',
		),
		'preview_theme_ob_filter' => array(
			'alt'     => '',
			'version' => '4.3',
		),
		'preview_theme_ob_filter_callback' => array(
			'alt'     => '',
			'version' => '4.3',
		),
		'wp_richedit_pre' => array(
			'alt'     => '',
			'version' => '4.3',
		),
		'wp_htmledit_pre' => array(
			'alt'     => '',
			'version' => '4.3',
		),
		'wp_ajax_wp_fullscreen_save_post' => array(
			'alt'     => '',
			'version' => '4.3',
		),

		'post_permalink' => array(
			'alt'     => 'get_permalink',
			'version' => '4.4',
		),
		'force_ssl_login' => array(
			'alt'     => 'force_ssl_admin',
			'version' => '4.4',
		),
		'create_empty_blog' => array(
			'alt'     => '',
			'version' => '4.4',
		),
		'get_admin_users_for_domain' => array(
			'alt'     => '',
			'version' => '4.4',
		),
		'wp_get_http' => array(
			'alt'     => 'WP_Http',
			'version' => '4.4',
		),
		// This is a method i.e. WP_Widget_Recent_Comments::flush_widget_cache() See #731.
		'flush_widget_cache' => array(
			'alt'     => '',
			'version' => '4.4',
		),

		'is_comments_popup' => array(
			'alt'     => '',
			'version' => '4.5',
		),
		'add_object_page' => array(
			'alt'     => 'add_menu_page()',
			'version' => '4.5',
		),
		'add_utility_page' => array(
			'alt'     => 'add_menu_page()',
			'version' => '4.5',
		),
		'get_comments_popup_template' => array(
			'alt'     => '',
			'version' => '4.5',
		),
		'comments_popup_script' => array(
			'alt'     => '',
			'version' => '4.5',
		),
		'popuplinks' => array(
			'alt'     => '',
			'version' => '4.5',
		),
		'get_currentuserinfo' => array(
			'alt'     => 'wp_get_current_user()',
			'version' => '4.5',
		),

		'wp_embed_handler_googlevideo' => array(
			'alt'     => '',
			'version' => '4.6',
		),
		'wp_get_sites' => array(
			'alt'     => 'get_sites()',
			'version' => '4.6',
		),

		// No deprecated functions in WordPress 4.7.
	);

	/**
	 * Groups of functions to restrict.
	 *
	 * Example: groups => array(
	 * 	'lambda' => array(
	 * 		'type'      => 'error' | 'warning',
	 * 		'message'   => 'Use anonymous functions instead please!',
	 * 		'functions' => array( 'eval', 'create_function' ),
	 * 	)
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		$groups = array();
		foreach ( $this->deprecated_functions as $deprecated_function => $data ) {
			$type = 'error';
			if ( version_compare( $data['version'], $this->minimum_supported_version, '>=' ) ) {
				$type = 'warning';
			}
			$message = '%s() has been deprecated since WordPress version ' . $data['version'] . '.';
			if ( ! empty( $data['alt'] ) ) {
				$message .= ' Use ' . $data['alt'] . ' instead.';
			}
			$groups[ $deprecated_function ] = array(
				'type'      => $type,
				'message'   => $message,
				'functions' => array(
					$deprecated_function,
				),
			);
		}

		return $groups;
	} // End getGroups()

}
