<?php

// NEVER PUSH DEBUG CALLS
// ----------------------

$post_id = get_the_ID();

var_dump( $post_id ); // Bad, forbidden use
print_r( $post_ID ); // Bad, forbidden use


// DEPRECATED PHP FUNCTIONS
// ------------------------

$title = get_the_title();

$title = ereg_replace( 'cool', 'not cool', get_the_title() ); // Bad, ereg_replace has been deprecated. Use preg_replace instead.
$title = preg_replace( 'cool', 'not cool', get_the_title() ); // Good

if ( ereg( '[A-Za-z]+', $title, $regs ) ) // Bad, ereg also deprecated. Use preg_match instead.
	die( $regs );

$title = eregi_replace( 'cool', 'not cool', get_the_title() ); // Bad, eregi_replace also deprecated. Use preg_replace instead.

list( $year, $month, $day ) = split( ':', $date ); // Bad, split has been deprecated. Use preg_split or explode instead.

$title_parts = spliti( ' ', get_the_title(), 4 ); // Bad, spliti also deprecated. Use preg_split instead.


// DEPRECATED WORDPRESS FUNCTIONS
// ------------------------------

$base_dir = find_base_dir(); // Bad, use WP_Filesystem::abspath instead.

$base_dir = get_base_dir(); // Bad, use WP_Filesystem::abspath instead.

dropdown_categories(); // Bad, use wp_link_category_checklist instead.

dropdown_link_categories(); // Bad, use wp_link_category_checklist instead.

$bookmark = get_link( $bookmark_id ); // Bad, use get_bookmark instead.

$catname = get_catname( $cat_id ); // Bad, use get_cat_name instead.

wp_setcookie( 'admin' ); // Bad, use wp_set_auth_cookie instead.

$cookie = wp_get_cookie_login(); // Bad

$login = wp_login( 'admin', 'pass' ); // Bad, use wp_signon instead.

$link = get_the_attachment_link(); // Bad, use wp_get_attachment_link instead.

$icon = get_attachment_icon_src(); // Bad, use wp_get_attachment_image_src instead.

$icon = get_attachment_icon(); // Bad, use wp_get_attachment_image instead.

$inner_html = get_attachment_innerHTML(); // Bad, use wp_get_attachment_image instead.


// AVOIDING MESS BY NOT CALLING THESE FUNCTIONS
// -------------------------------------------

register_globals(); // Bad

query_posts( 'post_type=attachment' ); // Bad, use WP_Query instead.
while ( have_posts() ){
	the_post();
	// ...
}
wp_reset_query(); // Bad, use wp_reset_postdata instead

$json = json_encode( $thing ); // Bad, use wp_json_encode instead.
