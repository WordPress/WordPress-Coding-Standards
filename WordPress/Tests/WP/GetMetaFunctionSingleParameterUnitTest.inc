<?php

// Invalid calls (missing mandatory parameter), but still useful to check that the sniff does not flag them.
$invalid_but_ok = get_post_meta();
$invalid_but_ok = get_post_meta( single: true, $post_id );
$invalid_but_ok = get_post_meta( single: true );
$invalid_but_ok = get_metadata( 'post' );

$ok = get_post_meta( $post_id );
$ok = get_post_meta( $post_id, $meta_key, false );
$ok = get_post_meta( $post_id, single: true );
$ok = get_post_meta( $post_id, single: true, key: $meta_key );
$ok = get_metadata( 'post', $post_id );
$ok = get_metadata( 'post', $post_id, $meta_key, true );
$ok = get_metadata( 'post', $post_id, single: true );
$ok = get_metadata( 'post', $post_id, single: true, meta_key: $meta_key );

$warning = get_post_meta( $post_id, $meta_key );
$warning = get_post_meta( $post_id, key: $meta_key );
$warning = get_post_meta( $post_id, key: $meta_key, sinngle: true ); // Typo in parameter name.
$warning = get_comment_meta( $comment_id, $meta_key );
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
