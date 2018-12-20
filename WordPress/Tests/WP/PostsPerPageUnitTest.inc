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
