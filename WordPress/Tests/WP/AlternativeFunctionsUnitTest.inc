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
