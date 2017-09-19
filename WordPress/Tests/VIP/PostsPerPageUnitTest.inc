<?php

$args = array(
	'nopaging'       => true, // Bad.
	'posts_per_page' => 999, // Bad.
	'posts_per_page' => -1, // Bad.
	'posts_per_page' => 1, // Ok.
	'posts_per_page' => '1', // Ok.
);

_query_posts( 'nopaging=true&posts_per_page=999' ); // Bad.

$query_args['posts_per_page'] = -1; // Bad.
$query_args['posts_per_page'] = 1; // Ok.
$query_args['posts_per_page'] = '1'; // Ok.
$query_args['posts_per_page'] = '-1'; // Bad.

$query_args['my_posts_per_page'] = -1; // Ok.

// @codingStandardsChangeSetting WordPress.VIP.PostsPerPage posts_per_page 50
 $query_args['posts_per_page'] = 50; // OK.
 $query_args['posts_per_page'] = 100; // Bad.
 $query_args['posts_per_page'] = 200; // Bad.
 $query_args['posts_per_page'] = 300; // Bad.
// @codingStandardsChangeSetting WordPress.VIP.PostsPerPage posts_per_page 200
 $query_args['posts_per_page'] = 50; // OK.
 $query_args['posts_per_page'] = 100; // OK.
 $query_args['posts_per_page'] = 200; // OK.
 $query_args['posts_per_page'] = 300; // Bad.
// @codingStandardsChangeSetting WordPress.VIP.PostsPerPage posts_per_page 100
