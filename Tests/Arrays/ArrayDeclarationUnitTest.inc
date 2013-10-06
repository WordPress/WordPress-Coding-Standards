<?php

$array_keyword_capitalize_first = Array(); // Bad

$array_keyword_must_be_lower_case = array(); // Good

$array_declaration_with_space_after_keyword = array (); // Bad

$space_between_parantheses_on_empty_array = array( ); // Bad

$query_vars = array_merge(
	array('post_type' => 'food'), // Bad, no spaces after opening and befoer closing paranthesis
	// ...
	array(
		'post_status' => 'private',
		'orderby' => 'title' // Bad, no comma at the ending
	)
);
$query = new WP_Query( $query_vars );

$query_vars = array_merge(
	array( 'post_type' => 'food' ), // Good
	// ...
	array(
		'post_status' => 'private',
		'orderby'     => 'title', // Good
	),
	foo() { // Good, Enclosures allowed
		return array();
	},
	bar( // Good, Functions allowed
		$bar,
		$taz
	),
);
$query = new WP_Query( $query_vars );

$defaults = array( 'type'=>'post' ), // Bad, no sapces before and after double arrow
wp_parse_args( $args, $defaults );
