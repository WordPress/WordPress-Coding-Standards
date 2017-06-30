<?php

switch_to_blog( $blogid ); // Error.

file_get_contents( $url ); // Warning.

vip_wp_file_get_contents( $url ); // Warning.

wp_remote_get( $url ); // Warning.





// Empty line - function moved to another sniff.

add_role( 'test' ); // Error.

array_pop( $array ); // Ok.

class Foo {
	function add_role() {} // Ok.
}
class Bar {
	static function add_role() {} // Ok.
}
$x = new Foo();
$x->add_role(); // Ok.
$y = Bar::add_role(); // Ok.
\SomeNamespace\add_role(); // Ok.

\add_role(); // Error.

get_term_link( $term ); // Error.

get_page_by_path( $path ); // Error.

get_page_by_title( $page_title ); // Error.

get_term_by( $field, $value, $taxonomy ); // Error.

get_category_by_slug( $slug ); // Error.

url_to_postid( $url ); // Error.

attachment_url_to_postid( $url ); // Error.
wpcom_vip_attachment_url_to_postid( $url ); // Ok.

get_tag_link(); // Error.
get_category_link(); // Error.
get_cat_ID(); // Error.
url_to_post_id(); // Error.

get_posts(); // Warning.
wp_get_recent_posts(); // Warning.

get_children(); // Warning.




term_exists(); // Error.
count_user_posts(); // Error.
wp_old_slug_redirect(); // Error.
get_adjacent_post(); // Error.
get_previous_post(); // Error.
get_next_post(); // Error.




get_previous_post_link(); // Error.
get_next_post_link(); // Error.
get_intermediate_image_sizes(); // Error.

wp_redirect(); // Warning.
wp_is_mobile(); // Error.

setcookie( 'cookie[three]', 'cookiethree' ); // Warning.

get_user_meta(); // Error.
update_user_meta(); // Error.
delete_user_meta(); // Error.
add_user_meta(); // Error.
