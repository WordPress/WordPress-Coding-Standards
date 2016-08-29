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
