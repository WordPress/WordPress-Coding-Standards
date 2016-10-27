<?php
// Make sure no groups are being excluded. Shouldn't be needed, but is due to some PHPCS test weirdness.
// @codingStandardsChangeSetting WordPress.WP.AlternativeFunctions exclude false

curl_close( $ch ); // Warning.
curl_copy_handle(); // Warning.
curl_errno(); // Warning.
curl_error(); // Warning.
curl_escape(); // Warning.
curl_exec(); // Warning.
curl_file_create(); // Warning.
CURL_getinfo(); // Warning - also test case insensitivity of the check.
curl_init(); // Warning.
curl_multi_add_handle(); // Warning.
curl_multi_close(); // Warning.
curl_multi_exec(); // Warning.
curl_multi_getcontent(); // Warning.
curl_multi_info_read(); // Warning.
curl_multi_init(); // Warning.
curl_multi_remove_handle(); // Warning.
curl_multi_select(); // Warning.
curl_multi_setopt(); // Warning.
curl_multi_strerror(); // Warning.
curl_pause(); // Warning.
curl_reset(); // Warning.
curl_setopt_array(); // Warning.
curl_setopt(); // Warning.
curl_share_close(); // Warning.
curl_share_init(); // Warning.
curl_share_setopt(); // Warning.
curl_strerror(); // Warning.
curl_unescape(); // Warning.
curl_version(); // Warning.

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
