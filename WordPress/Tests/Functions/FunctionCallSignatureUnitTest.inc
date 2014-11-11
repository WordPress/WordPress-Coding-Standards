<?php

add_filter('the_content', 'filter_function_name'); // Bad, no spaces after opening and before closing parenthesis
add_filter('the_content', 'filter_function_name' ); // Bad, no spaces after opening parenthesis
add_filter( 'the_content', 'filter_function_name'); // Bad, no spaces after closing parenthesis
add_filter( 'the_content', 'filter_function_name' ); // Good

$id = get_the_ID(); // Good

$id = get_the_ID( ); // Still good, but unnecessary space

$value = apply_filters( 'tag_name', 'value' ); // Good

function my_custom_shortcode( $atts ) {
	// OK! Without multilineCallParenthesis, opening parenthesis of a multi-line
	// function call can be the last content on the line. Ditto with the
	// closing parenthesis.
	extract( shortcode_atts( array(
		'param1' => 'val1',
		'param2' => 'val2',
	), $atts ) );

	// Also OK
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

$args = wp_parse_args( $args, array(
	'1' => 1,
	'2' => 2,
) );

if ( isset($var1) && isset( $var2 ) ) { // function call for $var1 is Bad, $var2 is good
	echo '';
}

if ( empty($var1) && empty( $var2 ) ) { // function call for $var1 is Bad, $var2 is good
	echo '';
}

/**
 * @see https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/249#issuecomment-59129708
 */
function bar() {
	foo(
		function () {

			echo 'Hello world!';
		}
	);
}

class Foo {

	/**
	 * Add Tags to post-content on archive pages
	 * @action loop_after_content, 30
	 */
	function action_post_meta_tags() {
		the_tags(
			sprintf( '<div class="tags-wrap"><strong>%s: </strong>', esc_html_x( 'Tags', 'archive loop' ) ),
			', ',
			'</div>'
		);
	}
}
