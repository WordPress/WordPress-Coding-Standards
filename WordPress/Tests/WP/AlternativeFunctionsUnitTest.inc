<?php



curl_init(); // Warning.
curl_close( $ch ); // Warning.
CURL_getinfo(); // Warning.
curlyhair(); // Ok.

parse_url( 'http://example.com/' ); // Warning.

$json = json_encode( $thing ); // Warning, use wp_json_encode instead.

file_get_contents(); // Warning.

readfile(); // Warning.
fopen(); // Warning.
fsockopen(); // Warning.
pfsockopen(); // Warning.
fclose(); // Warning.
fread(); // Warning.
fwrite(); // Warning.
file_put_contents(); // Warning.
strip_tags( $something ); // Warning.
rand(); // Warning.
mt_rand(); // Warning.
srand(); // Warning.
mt_srand(); // Warning.
wp_rand(); // OK.

// @codingStandardsChangeSetting WordPress.WP.AlternativeFunctions minimum_supported_version 4.0
parse_url( 'http://example.com/' ); // OK.
// @codingStandardsChangeSetting WordPress.WP.AlternativeFunctions minimum_supported_version 4.6

strip_tags( $something, '<iframe>' ); // OK.

// @codingStandardsChangeSetting WordPress.WP.AlternativeFunctions minimum_supported_version 4.0
parse_url($url, PHP_URL_QUERY); // OK.
// @codingStandardsChangeSetting WordPress.WP.AlternativeFunctions minimum_supported_version 4.7
parse_url($url, PHP_URL_SCHEME); // Warning.
// @codingStandardsChangeSetting WordPress.WP.AlternativeFunctions minimum_supported_version 4.6
