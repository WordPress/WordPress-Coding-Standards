<?php

/*
 * Not the sniff target.
 */
my\ns\get_post_meta($a, $b);
$this->get_post_meta($a, $b);
MyClass::get_post_meta($a, $b);
echo GET_POST_META;
add_action('my_action', get_post_meta(...)); // PHP 8.1 first class callable.

/*
 * These should all be okay.
 */
$ok = get_post_meta( $post_id );
$ok = get_post_meta( $post_id, $meta_key, false );
$ok = get_post_meta( $post_id, single: true );
$ok = get_post_meta( $post_id, single: true, key: $meta_key );
$ok = get_metadata( 'post', $post_id );
$ok = get_metadata( 'post', $post_id, $meta_key, true );
$ok = get_metadata( 'post', $post_id, single: true );
$ok = get_metadata( 'post', $post_id, single: true, meta_key: $meta_key );

// Incorrect function calls, should be ignored by the sniff.
$incorrect_but_ok = get_post_meta();
$incorrect_but_ok = get_post_meta( single: true, $post_id );
$incorrect_but_ok = get_post_meta( single: true );
$incorrect_but_ok = get_metadata( 'post' );

/*
 * These should all be flagged with a warning.
 */
$warning = \get_post_meta( $post_id, $meta_key );
implode(', ', get_post_meta( $post_id, $meta_key ));
if (get_post_meta( $post_id, key: $meta_key )) {}
$warning = get_post_meta( $post_id, key: $meta_key, sinngle: true ); // Typo in parameter name.
echo get_comment_meta( $comment_id, $meta_key );
$warning = get_site_meta( $site_id, $meta_key );
$warning = get_term_meta( $term_id, $meta_key );
$warning = get_user_meta( $user_id, $meta_key );
$warning = GET_METADATA( 'post', $post_id, $meta_key );
$warning = get_metadata(
    'post',
    $post_id,
    meta_key: $meta_key
);
$warning = get_metadata_raw( 'post', $post_id, $meta_key );
$warning = get_metadata_default( 'post', $post_id, $meta_key );
