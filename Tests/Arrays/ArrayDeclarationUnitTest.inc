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
	array(
		'closure' => function () { // Good, Closures allowed
			return array();
		},
	),
	bar( // Good, Functions allowed
		1,
		2
	)
);
$query = new WP_Query( $query_vars );

$defaults = array( 'type'=>'post' ), // Bad, no sapces before and after double arrow
wp_parse_args( $args, $defaults );

class Foo {
	function to_markdown( $params = array() ) {
		// Parse sections
		$section_formatters = array(
			'Description' => function ( $body ) use ( $params ) {
				if ( isset( $params['travis_ci_url'] ) ) {
					$body .= sprintf( "\n\n[![Build Status](%s.png)](%s)", $params['travis_ci_url'], $params['travis_ci_url'] );
				}
				return $body;
			},
			'Screenshots' => function ( $body ) {
				foreach ( $screenshot_matches as $i => $screenshot_match ) {
					print $i;
				}
			},
			'Food' => sprintf(
				'%s,%d',
				'a',
				1
			),
			'Bard' => add_query_arg(
				'quux',
				'bazd'
			)
		);
	}
}

$strings = array(
	'hidethings'			=> ( ( true === $this->settings['enable_hidden_class'] && ( is_array( $this->settings['hidden_classname'] ) && array() !== $this->settings['hidden_classname'] ) ) ? true : false ),
	'enable_async'			=> ( ( true === $this->settings['enable_async'] && ( is_array( $this->active_mimetypes ) && array() !== $this->active_mimetypes ) ) ? true : false ),
	'enable_async_debug'	=> ( ( true === $this->settings['enable_async_debug'] && ( is_array( $this->active_mimetypes ) && array() !== $this->active_mimetypes ) ) ? true : false ),
);
