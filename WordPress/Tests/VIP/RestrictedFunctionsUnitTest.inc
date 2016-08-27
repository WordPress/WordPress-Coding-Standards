<?php

switch_to_blog( $blogid ); // Bad.

eval( 'some_code' ); // Bad.

create_function( 'foo', 'bar(foo);' ); // Bad.

file_get_contents( $url ); // Bad.

vip_wp_file_get_contents( $url ); // Bad.

wp_remote_get( $url ); // Bad.

$ch = curl_init(); // Bad.

curl_close( $ch ); // Bad.

// Empty line - function moved to another sniff.

add_role( 'test' ); // Bad.

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

\add_role(); // Bad.

get_term_link( $term ); // Bad.

get_page_by_path( $path ); // Bad.

get_page_by_title( $page_title ); // Bad.

get_term_by( $field, $value, $taxonomy ); // Bad.

get_category_by_slug( $slug ); // Bad.

url_to_postid( $url ); // Bad.

attachment_url_to_postid( $url ); // Bad.
wpcom_vip_attachment_url_to_postid( $url ); // Ok.

get_tag_link(); // Error.
get_category_link(); // Error.
get_cat_ID(); // Error.
url_to_post_id(); // Error.
attachment_url_to_postid(); // Error.
get_posts(); // Warning.
wp_get_recent_posts(); // Warning.

get_children(); // Warning.
wp_get_post_terms(); // Error.
wp_get_post_categories(); // Error.
wp_get_post_tags(); // Error.
wp_get_object_terms(); // Error.
term_exists(); // Error.
count_user_posts(); // Error.
wp_old_slug_redirect(); // Error.
get_adjacent_post(); // Error.
get_previous_post(); // Error.
get_next_post(); // Error.
parse_url( 'http://example.com/' ); // Warning.

dl(); // Error.
error_reporting(); // Error.
ini_alter(); // Error.
ini_restore(); // Error.
ini_set(); // Error.
magic_quotes_runtime(); // Error.
set_magic_quotes_runtime(); // Error.
apache_setenv(); // Error.
putenv(); // Error.
set_include_path(); // Error.
restore_include_path(); // Error.
phpinfo(); // Error.

PHPINFO(); // Error.
CURL_getinfo(); // Error.

curlyhair(); // Ok.

get_previous_post_link(); // Error.
get_next_post_link(); // Error.
get_intermediate_image_sizes(); // Error.
serialize(); // Warning.
unserialize(); // Warning.
error_log(); // Error.
var_dump(); // Error.
print_r(); // Error.
trigger_error(); // Error.
set_error_handler(); // Error.
wp_redirect(); // Warning.
wp_is_mobile(); // Error.
urlencode(); // Warning.
rawurlencode(); // Ok.
