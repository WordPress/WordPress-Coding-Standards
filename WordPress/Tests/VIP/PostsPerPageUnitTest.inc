<?php

$args = array(
	'nopaging'       => true, // Bad.
	'posts_per_page' => 999, // OK.
	'posts_per_page' => -1, // Bad.
	'posts_per_page' => 1, // Ok.
	'posts_per_page' => '1', // Ok.
);

_query_posts( 'nopaging=true&posts_per_page=999' ); // Bad.

$query_args['posts_per_page'] = -1; // Bad.
$query_args['posts_per_page'] = 1; // OK.
$query_args['posts_per_page'] = '1'; // OK.
$query_args['posts_per_page'] = '-1'; // Bad.
$query_args['numberposts'] = '-1'; // Bad.

$query_args['my_posts_per_page'] = -1; // OK.

// Test deprecated property message.
// @codingStandardsChangeSetting WordPress.VIP.PostsPerPage posts_per_page 50
 $query_args['posts_per_page'] = 50; // OK.
// @codingStandardsChangeSetting WordPress.VIP.PostsPerPage posts_per_page 100
