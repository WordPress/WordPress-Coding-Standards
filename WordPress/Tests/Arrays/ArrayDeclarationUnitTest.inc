<?php

$array_keyword_capitalize_first = Array(); // Bad.

$array_keyword_must_be_lower_case = array(); // Good.

$array_declaration_with_space_after_keyword = array (); // Bad.

$space_between_parantheses_on_empty_array = array( ); // Bad.

$query_vars = array_merge(
	array('post_type' => 'food'), // Bad, no spaces after opening and before closing paranthesis.
	// ...
	array(
		'post_status' => 'private',
		'orderby' => 'title' // Bad, no comma at the ending.
	)
);
$query = new WP_Query( $query_vars );

$query_vars = array_merge(
	array( 'post_type' => 'food' ), // Good.
	// ...
	array(
		'post_status' => 'private',
		'orderby'     => 'title', // Good.
	),
	array(
		'closure' => function () { // Good, closures allowed.
			return array();
		},
	),
	bar( // Good, functions allowed.
		1,
		2
	)
);
$query = new WP_Query( $query_vars );

$defaults = array( 'type'=>'post' ); // Bad, no spaces before and after double arrow.
wp_parse_args( $args, $defaults );

class Foo {
	function to_markdown( $params = array() ) {
		// Parse sections.
		$section_formatters = array(
			'Description' => function ( $body ) use ( $params ) {
				if ( isset( $params['travis_ci_url'] ) ) {
					$body .= sprintf( "\n\n[![Build Status](%s.png)](%s)", $params['travis_ci_url'], $params['travis_ci_url'] );
				}
				return $body;
			},
			'Screenshots' => function ( $body ) {
				foreach ( $screenshot_matches as $i => $screenshot_match ) {
					print (int) $i;
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
			),
		);
	}
}

$primary_order   = array(
	'n', // Null.
	'b', // Boolean.
	'i', // Integer.
	'f', // Float.
	's', // String.
	'a', // Array.
	'o', // Object.
	'r', // Resource.
	'p', // SPL_Types object.
);

$var = array(
	'tab_template'      => '
		<li>%s</li>',
	'panel_template'        => '
		<div id="%s">
		    %s
		</div>',
);

// This should all be good.
$section_args = array(
	'title' => sprintf(
		__( 'Sidebar: %s', 'widget-customizer' ),
		$GLOBALS['wp_registered_sidebars'][ $sidebar_id ]['name']
	),
	'description' => $GLOBALS['wp_registered_sidebars'][ $sidebar_id ]['description'],
);

$strings = array(
	'hidethings'			=> ( ( true === $this->settings['enable_hidden_class'] && ( is_array( $this->settings['hidden_classname'] ) && array() !== $this->settings['hidden_classname'] ) ) ? true : false ),
	'enable_async'			=> ( ( true === $this->settings['enable_async'] && ( is_array( $this->active_mimetypes ) && array() !== $this->active_mimetypes ) ) ? true : false ),
	'enable_async_debug'	=> ( ( true === $this->settings['enable_async_debug'] && ( is_array( $this->active_mimetypes ) && array() !== $this->active_mimetypes ) ) ? true : false ),
);

$actions = array(
	'install' => sprintf(
		'<a href="%1$s" title="Install %2$s">Install</a>',
		wp_nonce_url(
			add_query_arg(
				array(
					'page'          => TGM_Plugin_Activation::$instance->menu,
					'plugin'        => $item['slug'],
					'plugin_name'   => $item['sanitized_plugin'],
					'plugin_source' => $item['url'],
					'tgmpa-install' => 'install-plugin',
				),
				admin_url( TGM_Plugin_Activation::$instance->parent_url_slug )
			),
			'tgmpa-install'
		),
		$item['sanitized_plugin']
	),
);

$custom_fields = array(
	'videoembed', // Press75 Simple Video Embedder.
	'_videoembed_manual', // Press75 Simple Video Embedder.
	'_videoembed', // Press75 Simple Video Embedder.
	'_premise_settings', // Premise.
);

$item_block_comment = array(
	'item',
	/* Some comment. */
);

$item_multiple_line_block_comment = array(
	'item',
	/* Some comment
	   over multiple lines. */
);

$block_comment = array(
	/* Just a comment. */
);

$comment = array(
	// Just a single comment.
);

$multidimensional_comments = array(
	'item_block_comment' => array(
		'item',
		/* Comment. */
	),
	'block_comment' => array(
		/* Comment. */
	),
	'item_comment' => array(
		'item',
		// Comment.
	),
	'comment' => array(
		// Comment.
	),
	// Comment.
	array(
		'item',
		/* Comment. */
	),
	array(
		/* Comment. */
	),
	array(
		'item',
		// Comment.
	),
	array(
		// Comment.
	),
	// Comment.
);

$q = new WP_Query( array(
	'meta_query' => array(
		'relation' => 'and', // Ok.
		array(
			'key' => 'asdsa',
			'value' => 'asds',
		),
	),
) );

array(
	'key',
	/**
	 * Doc comment.
	 */
//  'core'.
);

// Multiple values in an array on a single line is allowed.
array( 1, 2 );

// Test for fixing of extra whitespace.
array(   1, 2     );
