<?php

function hwl_home_pagesize( $query ) {
	if ( is_admin() || ! $query->is_main_query() )
		return;

	if ( is_home() ) {
		$query->set( 'posts_per_page', 1 );
	return; // Bad, not indented properly
	}

	// Bad, not indented properly
if ( is_post_type_archive( 'movie' ) ) {
		$query->set( 'posts_per_page', 50 );
		return;
	}
}
add_action( 'pre_get_posts', 'hwl_home_pagesize', 1 );

// Proper indentation in closure
add_filter(
	'the_content',
	function( $content ) {
		if ( is_home() )
			return $content;

		global $post;
		ob_start();
		?>
		<h2>Appended</h2>
		<?php
		$content .= ob_get_clean();
		return $content;
	}
);

/**
 * This is my function with doc comment.
*Indentation is ignored in
				* doc comment. Though this is not
				* considered at the moment, please indent
				* your doc comment properly.
 *
 * @param string $type Either 'foo' or 'bar'.
 * @param array|null $args Optional. Whether to use Foo or Bar. Default is null.
 * @return void
 */
function my_function_with_doc_comment( $type, $args = null ) {}

$string_spans_multiple_lines = 'First line.' .
	'Second line' .
		'Third line' .
			'second line'; // The sniff pass this though it's not indented properly.
