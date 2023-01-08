<?php

curl_init(); // Warning.
curl_close( $ch ); // Warning.
CURL_getinfo(); // Warning.
curlyhair(); // Ok.

parse_url( 'https://example.com/' ); // Warning.

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

strip_tags( $something, '<iframe>' ); // OK.

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
file_get_contents( 'php://input' ); // OK.

// Loosely related to issue 295.
file_get_contents( 'php://stdin' ); // OK.
$input_stream = fopen( 'php://stdin', 'w' ); // OK.
$csv_ar = fopen(STDIN); // OK.

$output_stream = fopen( 'php://output', 'w' ); // OK.
$output_stream = fopen( 'php://stdout', 'w' ); // OK.
$output_stream = fopen( 'php://stderr', 'w' ); // OK.
$output_stream = fopen( STDOUT, 'w' ); // OK.
$output_stream = fopen( STDERR, 'w' ); // OK.
$output_stream = fopen( 'php://fd/3', 'w' ); // OK.
$fp = fopen("php://temp/maxmemory:$fiveMBs", 'r+'); // OK.
readfile( 'php://filter/resource=http://www.example.com' ); // Warning.
file_put_contents("php://filter/write=string.rot13/resource=example.txt","Hello World"); // Warning.

curl_version(); // OK.

// Safeguard that additional logic uses case-insensitive function name check.
Strip_Tags( $something ); // Warning.

if ( ! $curl['features'] && CURL_VERSION_SSL ) {} // OK.
my_parse_url_function(); // OK.
function curl_version_ssl() {} // OK.
use function curl_version; // OK.
use function something as curl_version; // OK.
use function curl_init as curl_version; // Bad.

unlink(); // Warning.
rename(); // Warning.
chgrp(); // Warning.
chmod(); // Warning.
chown(); // Warning.
is_writable(); // Warning.
is_writeable(); // Warning.
mkdir(); // Warning.
rmdir(); // Warning.
touch(); // Warning.
fputs(); // Warning.

// phpcs:set WordPress.WP.AlternativeFunctions minimum_wp_version 4.0
parse_url( 'http://example.com/' ); // OK, alternative was not yet available.
// phpcs:set WordPress.WP.AlternativeFunctions minimum_wp_version 4.4
parse_url( 'http://example.com/' ); // Warning, not using $component param, so can switch over.
// phpcs:set WordPress.WP.AlternativeFunctions minimum_wp_version

// phpcs:set WordPress.WP.AlternativeFunctions minimum_wp_version 4.0
parse_url($url, PHP_URL_QUERY); // OK, alternative was not yet available.
// phpcs:set WordPress.WP.AlternativeFunctions minimum_wp_version 4.5
parse_url($url, PHP_URL_QUERY); // OK, $component param not yet available.
// phpcs:set WordPress.WP.AlternativeFunctions minimum_wp_version 4.7
parse_url($url, PHP_URL_SCHEME); // Warning, using $component param, but also using WP 4.7+, so can switch over.
// phpcs:set WordPress.WP.AlternativeFunctions minimum_wp_version

/*
 * Tests for support for PHP 8.0+ named parameters.
 */
// Safeguard support for PHP 8.0+ named parameters for the custom logic related to strip_tags().
strip_tags( allowed_tags: '<iframe>' ); // OK. Well not really, missing required param, but that's not the concern of this sniff.
strip_tags( allowed_tags: '<iframe>', string: $something ); // OK.
strip_tags( string: $something ); // Warning.

// Safeguard support for PHP 8.0+ named parameters for the custom logic related to parse_url().
// phpcs:set WordPress.WP.AlternativeFunctions minimum_wp_version 4.4
parse_url(component : PHP_URL_QUERY); // OK. Well, not really, missing required parameter, but that's not the concern of this sniff.
parse_url(component : PHP_URL_QUERY, url : $url); // OK.
parse_url(url : $url); // Warning.
// phpcs:set WordPress.WP.AlternativeFunctions minimum_wp_version 4.7
parse_url(component: PHP_URL_SCHEME, url: $url, ); // Warning.
// phpcs:set WordPress.WP.AlternativeFunctions minimum_wp_version

// Safeguard support for PHP 8.0+ named parameters for the custom logic related to file_get_contents().
file_get_contents( use_include_path: true, filename: $local_file, ); // OK.
file_get_contents( use_include_path: false, filename: $url, ); // Warning.

file_get_contents( use_include_path: false ); // OK, well not really, missing required param, but that's not the concern of this sniff.

file_get_contents( use_include_path: false, filename: 'http://remoteurl.com/file/?w=1' ); // Warning.
file_get_contents( use_include_path: false, filename: 'https://wordpress.org' ); // Warning.
file_get_contents( use_include_path: false, filename: ABSPATH . 'wp-admin/css/some-file.css'); // OK.
file_get_contents( use_include_path: false, filename: 'php://input' ); // OK.
file_get_contents(use_include_path: false, filename: MYABSPATH . 'plugin-file.json'); // Warning.

// Safeguard support for PHP 8.0+ named parameters for the custom logic related to fopen(), readfile() and file_put_contents().
fopen (mode: 'w'); // Warning. Missing required param, but that's not the concern of this sniff.
$output_stream = fopen( mode: 'w', filename: 'php://output', ); // OK.
$output_stream = fopen( mode: 'w', context: $stream, filename: STDOUT ); // OK.
$bytes = readfile( use_include_path: false, context: $stream, filename: 'php://fd/3' ); // OK.
file_put_contents( data: $data, flags : LOCK_EX, filename: STDERR ); // OK.
$output_stream = fopen( mode: 'r', filename: $url, ); // Warning.

// Safeguard that comments in the parameters are ignored for the custom logic related to file_get_contents().
file_get_contents(
	$local_file,
	true // Using local include path.
); // OK.
file_get_contents(
	// Not using an https: URL for reasons.
	ABSPATH . 'wp-admin/css/some-file.css'
); // OK.
file_get_contents(
	// Not using ABSPATH for reasons.
	$url
); // Warning.
file_get_contents(
	// Not using plugin_dir_path() for reasons.
	$url
); // Warning.
