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
