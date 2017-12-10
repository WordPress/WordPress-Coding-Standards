<?php
// DEPRECATED WORDPRESS FUNCTIONS.

/*
 * Error.
 */
/* ============ WP 0.71 ============ */
the_category_head();
the_category_ID();
/* ============ WP 1.2 ============ */
permalink_link();
/* ============ WP 1.5 ============ */
start_wp();
/* ============ WP 1.5.1 ============ */
get_postdata();
/* ============ WP 2.0 ============ */
create_user();
next_post();
previous_post();
user_can_create_draft();
user_can_create_post();
user_can_delete_post();
user_can_delete_post_comments();
user_can_edit_post();
user_can_edit_post_comments();
user_can_edit_post_date();
user_can_edit_user();
user_can_set_post_date();
/* ============ WP 2.1 ============ */
dropdown_cats();
get_archives();
get_author_link();
get_autotoggle();
get_link();
get_linkcatname();
get_linkobjectsbyname();
get_linkobjects();
get_linkrating();
get_links();
get_links_list();
get_links_withrating();
get_linksbyname();
get_linksbyname_withrating();
get_settings();
link_pages();
links_popup_script();
list_authors();
list_cats();
tinymce_include();
wp_get_links();
wp_get_linksbyname();
wp_get_post_cats();
wp_list_cats();
wp_set_post_cats();
/* ============ WP 2.2 ============ */
comments_rss();
/* ============ WP 2.3 ============ */
permalink_single_rss();
/* ============ WP 2.5 ============ */
comments_rss_link();
documentation_link();
get_attachment_icon();
get_attachment_icon_src();
get_attachment_innerHTML();
get_author_rss_link();
get_category_rss_link();
get_the_attachment_link();
gzip_compression();
wp_clearcookie();
wp_get_cookie_login();
wp_login();
wp_setcookie();
/* ============ WP 2.6 ============ */
dropdown_categories();
dropdown_link_categories();
/* ============ WP 2.7 ============ */
get_commentdata();
find_base_dir(); // Issue #731 - method WP_Filesystem_Base::find_base_dir() use WP_Filesystem::abspath instead.
get_base_dir(); // Issue #731 - method WP_Filesystem_Base::get_base_dir() use WP_Filesystem::abspath instead.
/* ============ WP 2.8 ============ */
__ngettext();
__ngettext_noop();
attribute_escape();
get_author_name();
get_catname();
get_category_children();
get_the_author_aim();
get_the_author_description();
get_the_author_email();
get_the_author_firstname();
get_the_author_icq();
get_the_author_ID();
get_the_author_lastname();
get_the_author_login();
get_the_author_msn();
get_the_author_nickname();
get_the_author_url();
get_the_author_yim();
js_escape();
register_sidebar_widget();
register_widget_control();
sanitize_url();
the_author_aim();
the_author_description();
the_author_email();
the_author_firstname();
the_author_icq();
the_author_ID();
the_author_lastname();
the_author_login();
the_author_msn();
the_author_nickname();
the_author_url();
the_author_yim();
unregister_sidebar_widget();
unregister_widget_control();
wp_specialchars();
/* ============ WP 2.9 ============ */
_c();
_nc();
get_real_file_to_edit();
make_url_footnote();
the_content_rss();
translate_with_context();
/* ============ WP 3.0 ============ */
activate_sitewide_plugin();
add_option_update_handler();
automatic_feed_links();
clean_url();
clear_global_post_cache();
codepress_footer_js();
codepress_get_lang();
deactivate_sitewide_plugin();
delete_usermeta();
funky_javascript_callback();
funky_javascript_fix();
generate_random_password();
get_alloptions();
get_blog_list();
get_most_active_blogs();
get_profile();
get_user_details();
get_usermeta();
get_usernumposts();
graceful_fail();
is_main_blog();
is_site_admin();
is_taxonomy();
is_term();
is_wpmu_sitewide_plugin();
mu_options();
remove_option_update_handler();
set_current_user();
update_usermeta();
use_codepress();
validate_email();
wp_dropdown_cats();
wp_shrink_dimensions();
wpmu_checkAvailableSpace();
wpmu_menu();
/* ============ WP 3.1 ============ */
get_author_user_ids();
get_dashboard_blog();
get_editable_authors();
get_editable_user_ids();
get_nonauthor_user_ids();
get_others_drafts();
get_others_pending();
get_others_unpublished_posts();
get_users_of_blog();
install_themes_feature_list();
is_plugin_page();
update_category_cache();
/* ============ WP 3.2 ============ */
favorite_actions();
wp_dashboard_quick_press_output();
wp_timezone_supported();
/* ============ WP 3.3 ============ */
add_contextual_help();
get_boundary_post_rel_link();
get_index_rel_link();
get_parent_post_rel_link();
get_user_by_email();
get_user_metavalues();
get_userdatabylogin();
index_rel_link();
is_blog_user();
media_upload_audio();
media_upload_file();
media_upload_image();
media_upload_video();
parent_post_rel_link();
sanitize_user_object();
screen_layout();
screen_meta();
screen_options();
start_post_rel_link();
the_editor();
type_url_form_audio();
type_url_form_file();
type_url_form_image();
type_url_form_video();
wp_admin_bar_dashboard_view_site_menu();
wp_preload_dialogs();
wp_print_editor_js();
wp_quicktags();
wp_tiny_mce();
wpmu_admin_do_redirect();
wpmu_admin_redirect_add_updated_param();
/* ============ WP 3.4 ============ */
add_custom_background();
add_custom_image_header();
clean_page_cache();
clean_pre();
current_theme_info();
debug_fclose();
debug_fopen();
debug_fwrite();
display_theme();
get_allowed_themes();
get_broken_themes();
get_current_theme();
get_site_allowed_themes();
get_theme();
get_theme_data();
get_themes();
logIO();
remove_custom_background();
remove_custom_image_header();
update_page_cache();
wp_explain_nonce();
wpmu_get_blog_allowedthemes();
/* ============ WP 3.5 ============ */
_flip_image_resource();
_get_post_ancestors();
_insert_into_post_button();
_media_button();
_rotate_image_resource();
_save_post_hook();
image_resize();
gd_edit_image_support();
get_default_page_to_edit();
get_post_to_edit();
get_udims();
sticky_class();
user_pass_ok();
wp_cache_reset();
wp_create_thumbnail();
wp_get_single_post();
wp_load_image();
/* ============ WP 3.6 ============ */
get_user_id_from_string();
wp_convert_bytes_to_hr();
wp_nav_menu_locations_meta_box();
/* ============ WP 3.7 ============ */
_search_terms_tidy();
get_blogaddress_by_domain();
the_attachment_links();
wp_update_core();
wp_update_plugin();
wp_update_theme();
/* ============ WP 3.8 ============ */
get_screen_icon();
screen_icon();
wp_dashboard_incoming_links();
wp_dashboard_incoming_links_control();
wp_dashboard_incoming_links_output();
wp_dashboard_plugins();
wp_dashboard_primary_control();
wp_dashboard_recent_comments_control();
wp_dashboard_secondary();
wp_dashboard_secondary_control();
wp_dashboard_secondary_output();
/* ============ WP 3.9 ============ */
_relocate_children();
default_topic_count_text();
format_to_post();
get_current_site_name();
rich_edit_exists();
wpmu_current_site();
/* ============ WP 4.0 ============ */
get_all_category_ids();
like_escape();
url_is_accessable_via_ssl();
/* ============ WP 4.1 ============ */
add_tab(); // Issue #731 - method, not function.
prepare_control(); // Issue #731 - method, not function.
print_tab_image(); // Issue #731 - method, not function.
remove_tab(); // Issue #731 - method, not function.
/* ============ WP 4.2 ============ */
prepreview_added_sidebars_widgets(); // Issue #731 - method, not function.
prepreview_added_widget_instance(); // Issue #731 - method, not function.
remove_prepreview_filters(); // Issue #731 - method, not function.
setup_widget_addition_previews(); // Issue #731 - method, not function.
/* ============ WP 4.3 ============ */
_preview_theme_stylesheet_filter();
_preview_theme_template_filter();
preview_theme();
preview_theme_ob_filter();
preview_theme_ob_filter_callback();
wp_ajax_wp_fullscreen_save_post();
wp_richedit_pre();
/* ============ WP 4.4 ============ */
create_empty_blog();
force_ssl_login();
get_admin_users_for_domain();
post_permalink();
wp_get_http();
flush_widget_cache(); // Issue #731 - method WP_Widget_Recent_Comments::flush_widget_cache()
/* ============ WP 4.5 ============ */
add_object_page();
add_utility_page();
comments_popup_script();
get_comments_popup_template();
get_currentuserinfo();
is_comments_popup();
popuplinks();

/*
 * Warning.
 */
/* ============ WP 4.6 ============ */
post_form_autocomplete_off();
wp_embed_handler_googlevideo();
wp_get_sites();
/* ============ WP 4.7 ============ */
_sort_nav_menu_items();
_usort_terms_by_ID();
_usort_terms_by_name();
get_paged_template();
wp_get_network();
wp_kses_js_entities();
/* ============ WP 4.8 ============ */
wp_dashboard_plugins_output();
/* ============ WP 4.9 ============ */
get_shortcut_link();
is_user_option_local();
wp_ajax_press_this_add_category();
wp_ajax_press_this_save_post();
