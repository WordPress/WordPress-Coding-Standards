<?php

add_post_meta( $post_id, 'key', 'value' ); // OK
add_post_meta( $post_id, 'key', 4 ); // OK

add_post_meta( $post_id, 'key', $value ); // Bad
add_post_meta( $post_id, 'key', wp_slash( $value ) ); // OK

add_post_meta( $post_id, 'key', some_func() ); // Bad
add_post_meta( $post_id, 'key', wp_slash( some_func() ) ); // OK

add_post_meta( $post_id, 'key', (int) $value ); // OK
add_post_meta( $post_id, 'key', (int) some_func() ); // OK

add_post_meta( $post_id, 'key', wp_slash( some_func() ) . wp_slash( $value ) ); // OK
add_post_meta( $post_id, 'key', some_func() . wp_slash( $value ) ); // Bad
add_post_meta( $post_id, 'key', wp_slash( some_func() ) . $value ); // Bad

add_post_meta( $post_id, wp_slash( $key ), $value ); // Bad
add_post_meta( $post_id, wp_slash( $key ), wp_slash( $value ) ); // OK

add_post_meta( $post_id, (int) $key, $value ); // Bad
add_post_meta( $post_id, (int) $key, (int) $value ); // OK

add_post_meta( $post_id, 'key', -4 ); // OK
add_post_meta( $post_id, 'key', array( 1, 2 ) ); // OK
add_post_meta( $post_id, 'key', array( 'a', 'b' ) ); // OK
add_post_meta( $post_id, 'key', array( 'key' => 'value', 'another' => 'val' ) ); // OK
add_post_meta( $post_id, 'key', array( 1 => 2, 3 => -4 ) ); // OK

add_post_meta( $post_id, 'key', array( $value, $test ) ); // Bad
add_post_meta( $post_id, 'key', array( wp_slash( $value ), $test ) ); // Bad
add_post_meta( $post_id, 'key', array( wp_slash( $value ), wp_slash( $test ) ) ); // OK
add_post_meta( $post_id, 'key', wp_slash( array( $value, $test ) ) ); // OK

wp_insert_comment( $data ); // Bad
wp_insert_comment( wp_slash( $data ) ); // OK

// Partly slashed function.
wp_update_term( $term_id, 'tag' ); // OK
wp_update_term( $term_id, 'tag', $data ); // Bad
wp_update_term( $term_id, 'tag', wp_slash( $data ) ); // OK
wp_update_term( $term_id, 'tag', array( 'parent' => $parent ) ); // OK
wp_update_term( $term_id, 'tag', array( 'parent' => $parent, 'name' => $name ) ); // Bad
wp_update_term( $term_id, 'tag', array( 'name' => $name ) ); // Bad
wp_update_term( $term_id, 'tag', array( 'parent' => $parent, 'name' => wp_slash( $name ) ) ); // OK
wp_update_term( $term_id, 'tag', array( 'name' => wp_slash( $name ) ) ); // OK
wp_update_term( $term_id, 'tag', array( 'parent' => wp_slash( $parent ) ) ); // OK
wp_update_term( $term_id, 'tag', array( 'parent' => wp_slash( $parent ), 'name' => wp_slash( $name  ) ) ); // OK
wp_update_term( $term_id, 'tag', array( 'name' => 'Name' ) ); // OK
wp_update_term( $term_id, 'tag', array( 'name' => -58 ) ); // OK

// Mixed slashing function.
wp_list_pages(); // OK.
wp_list_pages( $data ); // Warning.
wp_list_pages( wp_slash( $data ) ); // Warning.
wp_list_pages( array( 'meta_value' => $value, 'link_before' => $before ) ); // Bad.
wp_list_pages( array( 'meta_value' => wp_slash( $value ), 'link_before' => $before ) ); // OK.
wp_list_pages( wp_slash( array( 'meta_value' => $value, 'link_before' => $before ) ) ); // Warning.
wp_list_pages( array( 'meta_value' => wp_slash( $value ), 'link_before' => wp_slash( $before ) ) ); // Bad.
wp_list_pages( array( 'meta_value' => $value, 'link_before' => wp_slash( $before ) ) ); // Bad x 2.

// Function declarations and function calls within the declaration of an ignored
// function should both be ignored.
function add_comment_meta( $comment_id, $meta_key, $meta_value, $unique = false ) { // OK
	return add_metadata( 'comment', $comment_id, $meta_key, $meta_value, $unique ); // Warning x 2
}

function get_bookmarks( $r ) { // OK
	return get_term_by( 'name', $r['category_name'], 'link_category' ); // Warning
}

delete_metadata( 'user', 0, 'session_tokens', false, true ); // OK
delete_metadata( 'user', 0, 'session_tokens', array( false ), true ); // OK

// We need to make sure we don't blow up when an arg isn't set (meta value is optional).
delete_post_meta( $post_ID, 'meta_key' ); // OK
get_pages(); // OK

// Whitelisting.
add_post_meta( $post_id, 'key', $value ); // WPCS: slashing OK

// Some functions are ignored.
add_post_meta( $post_id, 'key', time() ); // OK
add_post_meta( $post_id, 'key', 'a-' . sanitize_key( 'lkjlkj' ) ); // OK

// Method names matching flagged functions should be ignored.
$obj->get_children( $args ); // OK

// Only an error when this is 'name'.
get_term_by( 'slug', $term ); // OK.
get_term_by( 'name', $term ); // Bad.
get_term_by( $by, $term ); // Bad.

// Slashed superglobals.
wpmu_validate_blog_signup( $_POST['blogname'], $_POST['blog_title'], $user ); // OK.

// Without whitespace.
get_bookmarks(array("category" => $cat->term_id)); // OK.

// String passed.
add_post_meta( $post_id, 'key', 'value' ); // OK.
add_post_meta( $post_id, 'key', 'I said, \"Wow!\"' ); // Warning.
add_post_meta( $post_id, 'key', 'That\'s nice.' ); // OK.
add_post_meta( $post_id, 'key', 'Slashes (\\).' ); // Warning.

wp_update_term( $term_id, 'tag', array( 'name' => 'value' ) ); // OK.
wp_update_term( $term_id, 'tag', array( 'name' => 'slash\\s' ) ); // Warning.

wp_list_pages( array( 'meta_value' => 'value' ) ); // OK.
wp_list_pages( array( 'meta_value' => 'slash\\s' ) ); // Warning.

// Mixed.
add_post_meta( $post_id, 'key', wp_slash( $value ) . $value . ' \"' ); // 1 Error, 1 Warning.

wp_update_term( $term_id, 'tag', array( 'name' => wp_slash( $value ) . $value . ' \"' ) ); // 1 Error, 1 Warning.

wp_list_pages( array( 'meta_value' => wp_slash( $value ) . $value . ' \"' ) ); // 1 Error, 1 Warning.

// More slashed superglobals.
wp_update_term( $term_id, 'tag', array( 'name' => $_POST['something'] ) ); // OK.
wp_list_pages( array( 'meta_value' => $_POST['something'] ) ); // OK.

// Without spaces.
get_term_by('slug', 'post-format-' . $value, 'post_format' );

// Slashed superglobals.
wp_insert_post( $_POST );

// Unknown key.
wp_update_term( $term_id, 'tag', array( $value => 'value' ) ); // Warning.

wp_list_pages( array( $value => 'value' ) ); // Warning.
