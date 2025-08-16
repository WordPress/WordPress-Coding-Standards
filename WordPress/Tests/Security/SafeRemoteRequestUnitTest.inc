<?php

wp_remote_get( $url ); // Warning.
wp_remote_post( $url, $args ); // Warning.
wp_remote_head( $url ); // Warning.
wp_remote_request( $url, $args ); // Warning.

// Safe alternatives - these should not trigger warnings.
wp_safe_remote_get( $url ); // OK.
wp_safe_remote_post( $url, $args ); // OK.
wp_safe_remote_head( $url ); // OK.
wp_safe_remote_request( $url, $args ); // OK.

// Test with different variable names and contexts.
$response = wp_remote_get( $user_input ); // Warning.
$result = wp_remote_post( $_GET['url'], array() ); // Warning.
$headers = wp_remote_head( $external_url ); // Warning.
$data = wp_remote_request( $api_endpoint, $request_args ); // Warning.

// Safe versions in context.
$response = wp_safe_remote_get( $user_input ); // OK.
$result = wp_safe_remote_post( $_GET['url'], array() ); // OK.
$headers = wp_safe_remote_head( $external_url ); // OK.
$data = wp_safe_remote_request( $api_endpoint, $request_args ); // OK.

// Test case sensitivity - function names should be case-insensitive.
WP_Remote_Get( $url ); // Warning.
WP_REMOTE_POST( $url, $args ); // Warning.
Wp_Remote_Head( $url ); // Warning.
wp_REMOTE_request( $url, $args ); // Warning.

// Other wp_remote_* functions that don't have safe alternatives shouldn't trigger warnings.
wp_remote_retrieve_body( $response ); // OK - This is for parsing responses, not making requests.
wp_remote_retrieve_response_code( $response ); // OK.
wp_remote_retrieve_headers( $response ); // OK.
wp_remote_retrieve_header( $response, 'Content-Type' ); // OK.
wp_remote_retrieve_response_message( $response ); // OK.
wp_remote_retrieve_cookie( $response, 'session' ); // OK.
wp_remote_retrieve_cookies( $response ); // OK.