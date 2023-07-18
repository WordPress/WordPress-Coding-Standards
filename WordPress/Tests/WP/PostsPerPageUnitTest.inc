<?php

$args = array(
	'posts_per_page' => 999, // Bad.
	'posts_per_page' => -1, // OK.
	'posts_per_page' => 1, // OK.
	'posts_per_page' => '1', // OK.
);

_query_posts( 'nopaging=true&posts_per_page=999' ); // Bad.
_query_posts( 'numberposts=999' ); // Bad.

$query_args['posts_per_page'] = 999; // Bad.
$query_args['posts_per_page'] = 1; // OK.
$query_args['posts_per_page'] = '1'; // OK.
$query_args['numberposts'] = '-1'; // OK.

$query_args['my_posts_per_page'] = -1; // OK.

// phpcs:set WordPress.WP.PostsPerPage posts_per_page 50
$query_args['posts_per_page'] = 50; // OK.
$query_args['posts_per_page'] = 100; // Bad.
$query_args['posts_per_page'] = 200; // Bad.
$query_args['posts_per_page'] = 300; // Bad.
// phpcs:set WordPress.WP.PostsPerPage posts_per_page 200
$query_args['posts_per_page'] = 50; // OK.
$query_args['posts_per_page'] = 100; // OK.
$query_args['posts_per_page'] = 200; // OK.
$query_args['posts_per_page'] = 300; // Bad.
// phpcs:set WordPress.WP.PostsPerPage posts_per_page 100

// phpcs:set WordPress.WP.PostsPerPage exclude[] posts_per_page
$query_args['posts_per_page'] = 300; // OK, group excluded.
// phpcs:set WordPress.WP.PostsPerPage exclude[]

// Ensure there will be no false positive for array access brackets when not used for an assignment.
$var = $query_args['posts_per_page']; // OK.
$firstChars = $text[0] . $text[1]; // OK.

// Text strings which are not query strings should be ignored.
_query_posts( 'numberposts' ); // OK.

// Assignments to non-string keys should be ignored. Note: PHP will auto-cast numeric strings to ints, so those should also be disregarded.
$var[10] = 300; // OK.
$var[] = 400; // OK.
$var['239'] = 500; // OK.

// Ensure the sniff disregards comments.
$query_args['posts_per_page' /* high */ ] = 999; // Bad.

$query_args['posts_per_page'] /* high */  = 999; // Bad.

$args = array(
	'posts_per_page' /* high */ => 999, // Bad.
);

$query_args['posts_per_page'] = /* high */ 999; // Bad.
$args = array(
	'posts_per_page' => /* high */ 999, // Bad.
);

// Safeguard that when a query string contains duplicate key, the value of the last one is used.
_query_posts( 'posts_per_page=999&nopaging=true&posts_per_page=50' ); // OK.

// Ensure the error gets reported on the key pointer.
$query_args[
	'posts_per_page'
] = 300; // Bad, error should be reported on the above line.

// Ensure that PHP 7.4 null coalesce equals get picked up on.
$query_args['posts_per_page'] ??= 50; // OK.
$query_args['posts_per_page'] ??= 200; // Bad.

// Ensure that the sniff does not report on PHP 8.0 match arms.
$val = match($val) {
	'posts_per_page' => 999, // OK, not an array assignment.
};

// Verify handling of arrays without trailing comma after the last array item.
$args = array( 'posts_per_page' => 999 ); // Bad.
$args = [
	'posts_per_page' => 999
]; // Bad.

// Verify that the complete value is being captured correctly and that non-numeric values are disregarded.
$args = array(
	'posts_per_page' => min( max( $first, $last ), $default_min ), // Should be ignored as undetermined.
	'posts_per_page' => 10 + $extra, // Should be ignored as undetermined.
	'posts_per_page' => $value[0][1], // Should be ignored as undetermined.
	'posts_per_page' => $value ? 10 * $value : 300, // Should be ignored as undetermined.
	'posts_per_page' => get_value( name: 'post_per_page', type: 'query' ), // Should be ignored as undetermined.
	'posts_per_page' => function($a): int {
		return do_something( $a );
	}, // Should be ignored as undetermined.
	'posts_per_page' => [ 100, 200, 300 ], // Should be ignored as undetermined.
	'posts_per_page' => array(100, 200, 300), // Should be ignored as undetermined.
);
$query_args['posts_per_page'] = my\get_posts_per_page(); // Should be ignored as undetermined.
$query_args['posts_per_page'] = '1e3'; // Should be ignored as undetermined. Would evaluate to 1000 with an int cast, but WP doesn't cast the value.

// Purely numeric strings should probably be accepted still as this won't make a difference for the database query.
$query_args['posts_per_page'] = '50'; // OK.
$query_args['posts_per_page'] = '200'; // Bad.
$query_args['posts_per_page'] = "200"; // Bad.

// Verify handling of explicitly positive numbers.
$args = array(
	'posts_per_page' => +50, // OK.
	'posts_per_page' => +200, // Bad.
);

// Verify handling of PHP 7.4+ numeric literals, PHP 8.1 octal literals, non-decimal numbers and floats.
$args = array(
	'posts_per_page' => 0b1001011, // OK (75).
	'posts_per_page' => 0b10010110, // Bad (150).
	'posts_per_page' => 0x4B, // OK (75).
	'posts_per_page' => 0x96, // Bad (150).
	'posts_per_page' => 0113, // OK (75).
	'posts_per_page' => 0226, // Bad (150).
	'posts_per_page' => 0O113, // OK (75).
	'posts_per_page' => 0o226, // Bad (150).
	'posts_per_page' => 7_5, // OK (75).
	'posts_per_page' => 1_50, // Bad (150).
	'posts_per_page' => 75.0, // OK (75).
	'posts_per_page' => 150.000, // Bad (150).
);
