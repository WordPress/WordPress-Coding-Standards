<?php

new WP_Query( array(
	'meta_query' => array(
		array(
			'key' => 'foo',
			'value' => 'bar',
		),
	),
	'tax_query' => array(
		array(
			'taxonomy' => 'foo',
		),
	),
	'meta_key' => 'foo',
	'meta_value' => 'bar',
) );

$query = 'foo=bar&meta_key=foo&meta_value=bar';

if ( ! isset( $widget['params'][0] ) ) {
	$widget['params'][0] = array();
}


// Testing whitelisting comments.
$test = array(

	// Single-line statements.
	'tax_query' => array(), // Bad.
	'tax_query' => array(), // WPCS: tax_query ok.

	// Multi-line statement.
	'tax_query' => array( // WPCS: tax_query ok.
		array(
			'taxonomy' => 'foo',
		),
	),
);
