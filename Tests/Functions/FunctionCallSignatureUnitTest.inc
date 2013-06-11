<?php

add_filter('the_content', 'filter_function_name'); // Bad, no spaces after opening and before closing paranthesis
add_filter( 'the_content', 'filter_function_name' ); // Good

$id = get_the_ID(); // Good

$id = get_the_ID( ); // Still good, but unnecessary space

$value = apply_filters( 'tag_name', 'value' ); // Good

function my_custom_shortcode( $atts ) {
	// Bad, opening paranthesis of a multi-line function call
	// must be the last content on the line. Ditto with the
	// closing parenthesis.
	extract( shortcode_atts( array(
		'param1' => 'val1',
		'param2' => 'val2',
	), $atts ) );

	// Good
	extract(
		shortcode_atts(
			array(
				'param1' => 'val1',
				'param2' => 'val2',
			),
			$atts
		)
	);

	return sprintf( '%s - %s', $param1, $param2 );
}
add_shortcode( 'my_custom', 'my_custom_shortcode' );

