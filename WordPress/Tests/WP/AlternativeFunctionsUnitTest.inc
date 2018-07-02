<?php



curl_init(); // Warning.
curl_close( $ch ); // Warning.
CURL_getinfo(); // Warning.
curlyhair(); // Ok.

parse_url( 'http://example.com/' ); // Warning.

$json = json_encode( $thing ); // Warning, use wp_json_encode instead.

file_get_contents( $url ); // Warning.

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

file_get_contents( $local_file, true ); // OK.
file_get_contents( $url, false ); // Warning.
file_get_contents(); // OK - no params, so nothing to do.
file_get_contents( 'http://remoteurl.com/file/?w=1' ); // Warning.
file_get_contents( 'https://wordpress.org' ); // Warning.
file_get_contents(ABSPATH . 'wp-admin/css/some-file.css'); // OK.
file_get_contents(MYABSPATH . 'plugin-file.json'); // Warning.
file_get_contents( MUPLUGINDIR . 'some-file.xml' ); // OK.
file_get_contents( plugin_dir_path( __FILE__ ) . 'subfolder/*.conf' ); // OK.
file_get_contents(WP_Upload_Dir()['path'] . 'subdir/file.inc'); // OK.
