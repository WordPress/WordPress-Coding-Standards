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
