<?php

switch_to_blog( $blogid ); // bad

eval( 'some_code' ); // bad

create_function( 'foo', 'bar(foo);' ); // bad

file_get_contents( $url ); // bad

vip_wp_file_get_contents( $url ); // bad

wp_remote_get( $url ); // bad

$ch = curl_init(); // bad

curl_close( $ch ); // bad

// Empty line - function moved to another sniff.

add_role( 'test' ); // bad

array_pop( $array ); // ok

class Foo {
	function add_role() {} // ok
}
class Bar {
	static function add_role() {} // ok
}
$x = new Foo();
$x->add_role(); // ok
$y = Bar::add_role(); // ok
\SomeNamespace\add_role(); // ok

\add_role(); // bad

get_term_link( $term ); // bad

get_page_by_path( $path ); // bad

get_page_by_title( $page_title ); // bad

get_term_by( $field, $value, $taxonomy ); // bad

get_category_by_slug( $slug ); // bad

url_to_postid( $url ); // bad

attachment_url_to_postid( $url ); // bad
wpcom_vip_attachment_url_to_postid( $url ); // ok

get_tag_link(); // error
get_category_link(); // error
get_cat_ID(); // error
url_to_post_id(); // error
attachment_url_to_postid(); // error
get_posts(); // warning
wp_get_recent_posts(); // warning

get_children(); // warning
wp_get_post_terms(); // error
wp_get_post_categories(); // error
wp_get_post_tags(); // error
wp_get_object_terms(); // error
term_exists(); // error
count_user_posts(); // error
wp_old_slug_redirect(); // error
get_adjacent_post(); // error
get_previous_post(); // error
get_next_post(); // error
parse_url( 'http://example.com/' ); // warning

dl(); // error
error_reporting(); // error
ini_alter(); // error
ini_restore(); // error
ini_set(); // error
magic_quotes_runtime(); // error
set_magic_quotes_runtime(); // error
apache_setenv(); // error
putenv(); // error
set_include_path(); // error
restore_include_path(); // error
phpinfo(); // error
