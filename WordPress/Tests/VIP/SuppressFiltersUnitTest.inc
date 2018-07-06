<?php

$args3 = array(
	'post_type' => 'post',
	'orderby' => 'date',
	'order' => 'ASC',
); // Bad.

get_posts( $args3 ); // Bad.
wp_get_recent_posts( $args3 ); // Bad.
get_children( $args3 ); // Bad.

get_posts( 'post_type=post&order=ASC' ); // Bad.
wp_get_recent_posts( 'post_type=post&order=ASC'); // Bad.
get_children( 'post_type=post&order=ASC' ); // Bad.

get_posts( 'post_type=post&suppress_filters=false&order=ASC' ); // Ok.
wp_get_recent_posts( 'post_type=post&suppress_filters=false&order=ASC'); // Ok.
get_children( 'post_type=post&suppress_filters=false&order=ASC' ); // Ok.

$args4 = array();
$args4['suppress_filters'] = 'true';

get_posts( $args4 ); // Bad.
wp_get_recent_posts( $args4 ); // Bad.
get_children( $args4 ); // Bad.

get_posts( array( 'post_type' => 'post', 'suppress_filters'=> true ) ); // Bad.
wp_get_recent_posts( array( 'post_type' => 'post', 'suppress_filters'=> true )  ); // Bad.
get_children( array( 'post_type' => 'post', 'suppress_filters'=> true )  ); // Bad.

$post_args                     = array();
$post_args['suppress_filters'] = true;

get_posts( $post_args ); // Bad.
wp_get_recent_posts( $post_args ); // Bad.
get_children( $post_args ); // Bad.

function test_suppress_filter() {
	$args = array( 'post_type' => 'post', 'suppress_filters' => true );
	get_posts( $args ); // Bad
	wp_get_recent_posts( $args ); // Bad
	get_children( $args ); // Bad

	$args_ok = array( 'post_type' => 'post', 'suppress_filters' => false );
	get_posts( $args_ok ); // Ok
	wp_get_recent_posts( $args_ok ); // Ok
	get_children( $args_ok ); // Ok
}