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

// Test with literal string URLs - these should NOT trigger warnings since they're not user-controlled.
wp_remote_get( 'https://api.example.com/data' ); // OK - literal string.
wp_remote_post( "https://api.wordpress.org/stats", $args ); // OK - literal string.
wp_remote_head( 'http://example.com' ); // OK - literal string.
wp_remote_request( 'https://secure-api.example.com/endpoint', $args ); // OK - literal string.

// Test with variable URLs - these SHOULD trigger warnings.
wp_remote_get( $dynamic_url ); // Warning.
wp_remote_post( get_option( 'api_url' ), $args ); // Warning.
wp_remote_head( $_POST['url'] ); // Warning.
wp_remote_request( $base_url . '/endpoint', $args ); // Warning.

// Test with concatenated strings - these SHOULD trigger warnings.
wp_remote_get( 'https://api.example.com/' . $endpoint ); // Warning.
wp_remote_post( $protocol . '://api.example.com', $args ); // Warning.

// Test with function calls returning URLs - these SHOULD trigger warnings.
wp_remote_get( home_url( '/api/v1' ) ); // Warning.
wp_remote_post( esc_url( $url ), $args ); // Warning.

// Test with class constants - these should NOT trigger warnings since they're not user-controlled.
wp_remote_get( MyClass::API_ENDPOINT ); // OK - class constant.
wp_remote_post( ApiConfig::BASE_URL, $args ); // OK - class constant.
wp_remote_head( self::API_URL ); // OK - class constant.
wp_remote_request( static::ENDPOINT_URL, $args ); // OK - class constant.

// Test with namespaced class constants - these should NOT trigger warnings.
wp_remote_get( \MyNamespace\Config::API_URL ); // OK - namespaced class constant.
wp_remote_post( My\Namespace\ApiClient::BASE_ENDPOINT, $args ); // OK - namespaced class constant.
