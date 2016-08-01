<?php

$args = array(
	'orderby' => 'rand', // Bad.
	"orderby" => "rand", // Bad.
	"orderby" => "RAND", // Bad.
);

_query_posts( 'orderby=rand' ); // Bad.

$query_args['orderby'] = 'rand'; // Bad.

$query_args['orderby'] = 'date'; // Ok.
