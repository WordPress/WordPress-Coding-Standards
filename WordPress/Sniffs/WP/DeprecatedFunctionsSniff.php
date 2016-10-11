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

	public $minimum_supported_version = 4.3;

	private $depreacted_functions = array(
		'the_category_id' => array(
			'alt'     => 'get_the_category()',
			'version' => '0.71'
		),
		'the_category_head' => array(
			'alt'     => 'get_the_category_by_ID()',
			'version' => '0.71'
		),

		'permalink_link' => array(
			'alt'     => 'the_permalink()',
			'version' => '1.2'
		),

		'start_wp' => array(
			'alt'     => 'the Loop',
			'version' => '1.5'
		),
		'get_postdata' => array(
			'alt'     => 'get_post()',
			'version' => '1.5.1'
		),

		'previous_post' => array(
			'alt'     => 'previous_post_link()',
			'version' => '2.0'
		),
		'next_post' => array(
			'alt'     => 'next_post_link()',
			'version' => '2.0'
		),
		'user_can_create_post' => array(
			'alt'     => 'current_user_can()',
			'2.0'
		),
		'user_can_create_draft' => array(
			'alt'     => 'current_user_can()',
			'2.0'
		),
		'user_can_edit_post' => array(
			'alt'     => 'current_user_can()',
			'2.0'
		),
		'user_can_delete_post' => array(
			'alt'     => 'current_user_can()',
			'2.0'
		),
		'user_can_set_post_date' => array(
			'alt'     => 'current_user_can()',
			'2.0'
		),
		'user_can_edit_post_comments' => array(
			'alt'     => 'current_user_can()',
			'2.0'
		),
		'user_can_delete_post_comments' => array(
			'alt'     => 'current_user_can()',
			'2.0'
		),
		'user_can_edit_user' => array(
			'alt'     => 'current_user_can()',
			'2.0'
		),
		'create_user' => array(
			'alt'     => 'wp_create_user()',
			'version' => '2.0'
		),

		'get_linksbyname' => array(
			'alt'     => 'get_bookmarks()',
			'version' => '2.1'
		),
		'wp_get_linksbyname' => array(
			'alt'     => 'wp_list_bookmarks()',
			'version' => '2.1'
		),
		'get_linkobjectsbyname' => array(
			'alt'     => 'get_bookmarks()',
			'version' => '2.1'
		),
		'get_linkobjects' => array(
			'alt'     => 'get_bookmarks()',
			'version' => '2.1'
		),
		'get_linksbyname_withrating' => array(
			'alt'     => 'get_bookmarks()',
			'version' => '2.1'
		),
		'get_links_withrating' => array(
			'alt'     => 'get_bookmarks()',
			'version' => '2.1'
		),
		'get_autotoggle' => array(
			'alt'     => '',
			'version' => '2.1'
		),
		'list_cats' => array(
			'alt'     => 'wp_list_categories',
			'version' => '2.1'
		),
		'wp_list_cats' => array(
			'alt'     => 'wp_list_categories',
			'version' => '2.1'
		),
		'dropdown_cats' => array(
			'alt'     => 'wp_dropdown_categories()',
			'version' => '2.1'
		),
		'list_authors' => array(
			'alt'     => 'wp_list_authors()',
			'version' => '2.1'
		),
		'wp_get_post_cats' => array(
			'alt'     => 'wp_get_post_categories()',
			'version' => '2.1'
		),
		'wp_set_post_cats' => array(
			'alt'     => 'wp_set_post_categories()',
			'version' => '2.1'
		),
		'get_archives' => array(
			'alt'     => 'wp_get_archives',
			'version' => '2.1'
		),
		'get_author_link' => array(
			'alt'     => 'get_author_posts_url()',
			'version' => '2.1'
		),
		'link_pages' => array(
			'alt'     => 'wp_link_pages()',
			'version' => '2.1'
		),
		'get_settings' => array(
			'alt'     => 'get_option()',
			'version' => '2.1'
		),
		'wp_get_links' => array(
			'alt'     => 'wp_list_bookmarks()',
			'version' => '2.1'
		),
		'get_links' => array(
			'alt'     => 'get_bookmarks()',
			'version' => '2.1'
		),
//		'get_links_list' => array(
//			'alt'     => 'wp_list_bookmarks()',
//			'version' => '2.1'
//		),
		'links_popup_script' => array(
			'alt'     => '',
			'version' => '2.1'
		),
		'get_linkrating' => array(
			'alt'     => 'sanitize_bookmark_field()',
			'version' => '2.1'
		),
		'get_linkcatname' => array(
			'alt'     => 'get_category()',
			'version' => '2.1'
		),
		'get_link' => array(
			'alt'     => 'get_bookmark()',
			'version' => '2.1'
		),
		'tinymce_include' => array(
			'alt'     => 'wp_tiny_mce()',
			'version' => '2.1'
		),

//		'comments_rss' => array( 'get_post_comments_feed_link()', '2.2' ),
//
//		'permalink_single_rss' => array( 'permalink_rss()', '2.3' ),
//
//		'comments_rss_link' => array( 'post_comments_feed_link()', '2.5' ),
//		'get_category_rss_link' => array( 'get_category_feed_link()', '2.5' ),
//		'get_author_rss_link' => array( 'get_author_feed_link()', '2.5' ),
//		'get_the_attachment_link' => array( 'wp_get_attachment_link()', '2.5' ),
//		'get_attachment_icon_src' => array( 'wp_get_attachment_image_src()', '2.5' ),
//		'get_attachment_icon' => array( 'wp_get_attachment_image()', '2.5' ),
//		'get_attachment_innerhtml' => array( 'wp_get_attachment_image()', '2.5' ),
//		'documentation_link' => array( '', '2.5' ),
//
//		'gzip_compression' => array( '', '2.5' ),
//
//		'dropdown_categories' => array( 'wp_category_checklist()','2.6' ),
//		'dropdown_link_categories' => array( 'wp_link_category_checklist()','2.6' ),
//
//		'get_commentdata' => array( 'get_comment()', '2.7' ),
//
//		'get_catname' => array( 'get_cat_name()', '2.8' ),
//		'get_category_children' => array( 'get_term_children', '2.8' ),
//		'get_the_author_description' => array( 'get_the_author_meta(\'description\')', '2.8' ),
//		'the_author_description' => array( 'the_author_meta(\'description\')', '2.8' ),
//		'get_the_author_login' => array( 'the_author_meta(\'login\')', '2.8' ),
//		'get_the_author_firstname' => array( 'get_the_author_meta(\'first_name\')', '2.8' ),
//		'the_author_firstname' => array( 'the_author_meta(\'first_name\')', '2.8' ),
//		'get_the_author_lastname' => array( 'get_the_author_meta(\'last_name\')', '2.8' ),
//		'the_author_lastname' => array( 'the_author_meta(\'last_name\')', '2.8' ),
//		'get_the_author_nickname' => array( 'get_the_author_meta(\'nickname\')', '2.8' ),
//		'the_author_nickname' => array( 'the_author_meta(\'nickname\')', '2.8' ),
//		'get_the_author_email' => array( 'get_the_author_meta(\'email\')', '2.8' ),
//		'the_author_email' => array( 'the_author_meta(\'email\')', '2.8' ),
//		'get_the_author_icq' => array( 'get_the_author_meta(\'icq\')', '2.8' ),
//		'the_author_icq' => array( 'the_author_meta(\'icq\')', '2.8' ),
//		'get_the_author_yim' => array( 'get_the_author_meta(\'yim\')', '2.8' ),
//		'the_author_yim' => array( 'the_author_meta(\'yim\')', '2.8' ),
//		'get_the_author_msn' => array( 'get_the_author_meta(\'msn\')', '2.8' ),
//		'the_author_msn' => array( 'the_author_meta(\'msn\')', '2.8' ),
//		'get_the_author_aim' => array( 'get_the_author_meta(\'aim\')', '2.8' ),
//		'the_author_aim' => array( 'the_author_meta(\'aim\')', '2.8' ),
//		'get_author_name' => array( 'get_the_author_meta(\'display_name\')', '2.8' ),
//		'get_the_author_url' => array( 'get_the_author_meta(\'url\')', '2.8' ),
//		'the_author_url' => array( 'the_author_meta(\'url\')', '2.8' ),
//		'get_the_author_ID' => array( 'get_the_author_meta(\'ID\')', '2.8' ),
//		'the_author_ID' => array( 'the_author_meta(\'ID\')', '2.8' ),
//		'__ngettext' => array( '_n_noop()', '2.8' ),
//		'__ngettext_noop' => array( '_n_noop()', '2.8' ),
//		'sanitize_url' => array( 'esc_url()', '2.8' ),
//		'js_escape' => array( 'esc_js()', '2.8' ),
//		 'wp_specialchars' => array( 'esc_html()', '2.8' ),
//		'attribute_escape' => array( 'esc_attr()', '2.8' ),
//		'register_sidebar_widget' => array( 'wp_register_sidebar_widget()', '2.8' ),
//		'unregister_sidebar_widget' => array( 'wp_unregister_sidebar_widget()', '2.8' ),
//		'register_widget_control' => array( 'wp_register_widget_control()', '2.8' ),
//		'unregister_widget_control' => array( 'wp_unregister_widget_control()', '2.8' ),
//
//		array( 'the_content_rss' => 'the_content_feed()', '2.9' ),
//		array( 'make_url_footnote' => '', '2.9' ),
//		array( '_c' => '_x()', '2.9' ),
//
//		array( 'translate_with_context' => '_x()', '3.0' ),
//		array( 'nc' => 'nx()', '3.0' ),
//		array( 'get_alloptions' => 'wp_load_alloptions()', '3.0' ),
//		array( 'clean_url' => 'esc_url()', '3.0' ),
//		array( 'delete_usermeta' => 'delete_user_meta()', '3.0' ),
//		array( 'get_usermeta' => 'get_user_meta()', '3.0' ),
//		array( 'update_usermeta' => 'update_user_meta()', '3.0' ),
//		array( 'automatic_feed_links' => 'add_theme_support( \'automatic-feed-links\' )', '3.0' ),
//		array( 'get_profile' => 'get_the_author_meta()', '3.0' ),
//		array( 'get_usernumposts' => 'count_user_posts()', '3.0' ),
//		array( 'funky_javascript_callback' => '', '3.0' ),
//		array( 'funky_javascript_fix' => '', '3.0' ),
//		array( 'is_taxonomy' => 'taxonomy_exists()', '3.0' ),
//		array( 'is_term' => 'term_exists()', '3.0' ),
//		array( 'wp_dropdown_cats' => 'wp_dropdown_categories()','3.0' ),
//		array( 'add_option_update_handler' => 'register_setting()','3.0' ),
//		array( 'remove_option_update_handler' => 'unregister_setting()','3.0' ),
//		array( 'codepress_get_lang' => '','3.0' ),
//		array( 'codepress_footer_js' => '','3.0' ),
//		array( 'use_codepress' => '','3.0' ),
//		array( 'wp_shrink_dimensions' => 'wp_constrain_dimensions()','3.0' ),
//
//		array( 'is_plugin_page' => '$plugin_page and/or get_plugin_page_hookname() hooks', '3.1' ),
//		array( 'update_category_cache' => 'No alternatives', '3.1' ),
//		array( 'get_users_of_blog' => 'get_users()', '3.1' ),
//		array( 'get_author_user_ids' => '','3.1' ),
//		array( 'get_editable_authors' => '','3.1' ),
//		array( 'get_editable_user_ids' => '','3.1' ),
//		array( 'get_nonauthor_user_ids' => '','3.1' ),
//		array( 'WP_User_Search' => 'WP_User_Query','3.1' ),
//		array( 'get_others_unpublished_posts' => '','3.1' ),
//		array( 'get_others_drafts' => '','3.1' ),
//		array( 'get_others_pending' => '', '3.1' ),
//
//		array( 'wp_timezone_supported' => '', '3.2' ),
//		array( 'wp_dashboard_quick_press' => '', '3.2' ),
//		array( 'wp_tiny_mce' => 'wp_editor', '3.2' ),
//		array( 'wp_preload_dialogs' => 'wp_editor()', '3.2' ),
//		array( 'wp_print_editor_js' => 'wp_editor()', '3.2' ),
//		array( 'wp_quicktags' => 'wp_editor()', '3.2' ),
//		array( 'favorite_actions' => 'WP_Admin_Bar', '3.2' ),
//
//		array( 'the_editor' => 'wp_editor', '3.3' ),
//		array( 'get_user_metavalues' => '', '3.3' ),
//		array( 'sanitize_user_object' => '', '3.3' ),
//		array( 'get_boundary_post_rel_link' => '', '3.3' ),
//		array( 'start_post_rel_link' => 'none available ', '3.3' ),
//		array( 'get_index_rel_link' => '', '3.3' ),
//		array( 'index_rel_link' => '', '3.3' ),
//		array( 'get_parent_post_rel_link' => '', '3.3' ),
//		array( 'parent_post_rel_link' => '', '3.3' ),
//		array( 'wp_admin_bar_dashboard_view_site_menu' => '', '3.3' ),
//		array( 'is_blog_user' => 'is_member_of_blog()', '3.3' ),
//		array( 'debug_fopen' => 'error_log()', '3.3' ),
//		array( 'debug_fwrite' => 'error_log()', '3.3' ),
//		array( 'debug_fclose' => 'error_log()', '3.3' ),
//		array( 'screen_layout' => '$current_screen->render_screen_layout()', '3.3' ),
//		array( 'screen_options' => '$current_screen->render_per_page_options()', '3.3' ),
//		array( 'screen_meta' => ' $current_screen->render_screen_meta()', '3.3' ),
//		array( 'media_upload_image' => 'wp_media_upload_handler()', '3.3' ),
//		array( 'media_upload_audio' => 'wp_media_upload_handler()', '3.3' ),
//		array( 'media_upload_video' => 'wp_media_upload_handler()', '3.3' ),
//		array( 'media_upload_file' => 'wp_media_upload_handler()', '3.3' ),
//		array( 'type_url_form_image' => 'wp_media_insert_url_form( \'image\' )', '3.3' ),
//		array( 'type_url_form_audio' => 'wp_media_insert_url_form( \'audio\' )', '3.3' ),
//		array( 'type_url_form_video' => 'wp_media_insert_url_form( \'video\' )', '3.3' ),
//		array( 'type_url_form_file' => 'wp_media_insert_url_form( \'file\' )', '3.3' ),
//		array( 'add_contextual_help' => 'get_current_screen()->add_help_tab()', '3.3' ),
//
//		array( 'get_themes' => 'wp_get_themes()', '3.4' ),
//		array( 'get_theme' => 'wp_get_theme()', '3.4' ),
//		array( 'get_current_theme' => 'wp_get_theme()', '3.4' ),
//		array( 'clean_pre' => '', '3.4' ),
//		array( 'add_custom_image_header' => 'add_theme_support( \'custom-header\', $args )', '3.4' ),
//		array( 'remove_custom_image_header' => 'remove_theme_support( \'custom-header\' )', '3.4' ),
//		array( 'add_custom_background' => 'add_theme_support( \'custom-background\', $args )', '3.4' ),
//		array( 'remove_custom_background' => 'remove_theme_support( \'custom-background\' )', '3.4' ),
//		array( 'get_theme_data' => 'wp_get_theme()', '3.4' ),
//		array( 'update_page_cache' => 'update_post_cache()', '3.4' ),
//		array( 'clean_page_cache' => 'clean_post_cache()', '3.4' ),
//		array( 'get_allowed_themes' => 'wp_get_themes( array( \'allowed\' => true ) )', '3.4' ),
//		array( 'get_broken_themes' => 'wp_get_themes( array( \'errors\' => true )', '3.4' ),
//		array( 'current_theme_info' => 'wp_get_theme()', '3.4' ),
//		array( 'wp_explain_nonce' => 'wp_nonce_ays', '3.4.1' ),
//
//		array( 'sticky_class' => 'post_class()', '3.5' ),
//		array( '_get_post_ancestors' => '', '3.5' ),
//		array( 'wp_load_image' => 'wp_get_image_editor()', '3.5' ),
//		array( 'image_resize' => 'wp_get_image_editor()', '3.5' ),
//		array( 'wp_get_single_post' => 'get_post()', '3.5' ),
//		array( 'user_pass_ok' => 'wp_authenticate()', '3.5' ),
//		array( '_save_post_hook' => '', '3.5' ),
//		array( 'gd_edit_image_support' => 'wp_image_editor_supports', '3.5' ),
//		array( '_insert_into_post_button' => '', '3.5' ),
//		array( '_media_button' => '', '3.5' ),
//		array( 'get_post_to_edit' => 'get_post()', '3.5' ),
//		array( 'get_default_page_to_edit' => 'get_default_post_to_edit()', '3.5' ),
//		array( 'wp_create_thumbnail' => 'image_resize()', '3.5' ),

		'get_user_id_from_string' => array(
			'alt'     => 'get_user_by()',
			'version' => '3.6'
		),
		'wp_convert_bytes_to_hr' => array(
			'alt'     => 'size_format()',
			'version' => '3.6'
		),
		'wp_nav_menu_locations_meta_box' => array(
			'alt'     => '',
			'version' => '3.6'
		),

		'the_attachment_links' => array(
			'alt'     => '',
			'version' => '3.7'
		),
		'wp_update_core' => array(
			'alt'     => 'new Core_Upgrader()',
			'version' => '3.7'
		),
		'wp_update_plugin' => array(
			'alt'     => 'new Plugin_Upgrader()',
			'version' => '3.7'
		),
		'wp_update_theme' => array(
			'alt'     => 'new Theme_Upgrader()',
			'version' => '3.7'
		),
		'_search_terms_tidy' => array(
			'alt'     => '',
			'version' => '3.7'
		),
		'get_blogaddress_by_domain' => array(
			'alt'     => '',
			'version' => '3.7'
		),

		'get_screen_icon' => array(
			'alt'     => '',
			'version' => '3.8'
		),
		'screen_icon' => array(
			'alt'     => '',
			'version' => '3.8'
		),
		'wp_dashboard_incoming_links' => array(
			'alt'     => '',
			'version' => '3.8'
		),
		'wp_dashboard_incoming_links_control' => array(
			'alt'     => '',
			'version' => '3.8'
		),
		'wp_dashboard_incoming_links_output' => array(
			'alt'     => '',
			'version' => '3.8'
		),
		'wp_dashboard_plugins' => array(
			'alt'     => '',
			'version' => '3.8'
		),
		'wp_dashboard_primary_control' => array(
			'alt'     => '',
			'version' => '3.8'
		),
		'wp_dashboard_recent_comments_control' => array(
			'alt'     => '',
			'version' => '3.8'
		),
		'wp_dashboard_secondary' => array(
			'alt'     => '',
			'version' => '3.8'
		),
		'wp_dashboard_secondary_control' => array(
			'alt'     => '',
			'version' => '3.8'
		),
		'wp_dashboard_secondary_output' => array(
			'alt'     => '',
			'version' => '3.8'
		),

		'rich_edit_exists' => array(
			'alt'     => '',
			'version' => '3.9'
		),
		'default_topic_count_text' => array(
			'alt'     => '',
			'version' => '3.9'
		),
		'format_to_post' => array(
			'alt'     => '',
			'version' => '3.9'
		),
		'get_current_site_name' => array(
			'alt'     => 'get_current_site()',
			'version' => '3.9'
		),
		'wpmu_current_site' => array(
			'alt'     => '',
			'version' => '3.9'
		),
		'_relocate_children' => array(
			'alt'     => '',
			'version' => '3.9'
		),

		'get_all_category_ids' => array(
			'alt'     => 'get_terms()',
			'version' => '4.0'
		),
		'like_escape' => array(
			'alt'     => 'wpdb::esc_like()',
			'version' => '4.0'
		),
		'url_is_accessable_via_ssl' => array(
			'alt'     => '',
			'version' => '4.0'
		),

		'prepare_control' => array(
			'alt'     => '',
			'version' => '4.1'
		),
		'add_tab' => array(
			'alt'     => '',
			'version' => '4.1'
		),
		'remove_tab' => array(
			'alt'     => '',
			'version' => '4.1'
		),
		'print_tab_image' => array(
			'alt'     => '',
			'version' => '4.1'
		),

		'setup_widget_addition_previews' => array(
			'alt'     => 'customize_dynamic_setting_args',
			'version' => '4.2'
		),
		'prepreview_added_sidebars_widgets' => array(
			'alt'     => 'customize_dynamic_setting_args',
			'version' => '4.2'
		),
		'prepreview_added_widget_instance' => array(
			'alt'     => 'customize_dynamic_setting_args',
			'version' => '4.2'
		),
		'remove_prepreview_filters' => array(
			'alt'     => 'customize_dynamic_setting_args()',
			'version' => '4.2'
		),

		'wp_get_http' => array(
			'alt'     => 'WP_Http',
			'version' => '4.4'
		),

		'is_comments_popup' => array(
			'alt'     => '',
			'version' => '4.5'
		),
		'add_object_page' => array(
			'alt'     => 'add_menu_page()',
			'version' => '4.5'
		),
		'add_utility_page' => array(
			'alt'     => 'add_menu_page()',
			'version' => '4.5'
		),
		'get_comments_popup_template' => array(
			'alt'     => '',
			'version' => '4.5'
		),
		'comments_popup_script' => array(
			'alt'     => '',
			'version' => '4.5'
		),
		'popuplinks' => array(
			'alt'     => '',
			'version' => '4.5'
		),
		'get_currentuserinfo' => array(
			'alt'     => 'wp_get_current_user()',
			'version' => '4.5'
		),

		'wp_embed_handler_googlevideo' => array(
			'alt'     => '',
			'version' => '4.6'
		),
		'wp_get_sites' => array(
			'alt'     => 'get_sites()',
			'version' => '4.6'
		),
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
		foreach ( $this->depreacted_functions as $depreacted_function => $data ) {
			if ( intval( $data['version'] < $this->minimum_supported_version ) ) {
				$type = 'error';
			} else {
				$type = 'warning';
			}
			if ( empty( $data['alt'] ) ) {
				$message = $depreacted_function . '() has been deprecated since version ' . $data['version'] . '.';
			} else {
				$message = $depreacted_function . '() has been deprecated since version ' . $data['version'] . '. Use ' . $data['alt'] . ' instead.';
			}
			$groups[ $depreacted_function ] = array(
				'type'      => $type,
				'message'   => $message,
				'functions' => array(
					$depreacted_function
				),
			);
		}

		return $groups;
	} // End getGroups()
}
