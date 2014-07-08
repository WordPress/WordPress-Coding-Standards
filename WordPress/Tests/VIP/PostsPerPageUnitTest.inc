<?php

$args = array(
	'nopaging'       => true, // Bad
	'posts_per_page' => 999, // Bad
	'posts_per_page' => -1, // Bad
	);

_query_posts( 'nopaging=true&posts_per_page=999' ); // Bad

$query_args['posts_per_page'] = -1; // Bad

$query_args['my_posts_per_page'] = -1; // ok
