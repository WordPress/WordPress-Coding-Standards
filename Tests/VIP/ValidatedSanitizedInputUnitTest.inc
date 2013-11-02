<?php

do_something( $_POST ); // Warning, unusual usage

do_something_with( $_POST['hello'] ); // Error for no validation, Error for no sanitizing

echo esc_html( $_POST['foo'] ); // Error for no validation

if ( isset( $_POST['foo'] ) ) {
	bar( $_POST['foo'] ); // Error for no sanitizing
}

// @TODO: Cover non-parenthesis'd conditions
// if ( isset( $_POST['foo'] ) ) 
// 	bar( $_POST['foo'] );


if ( isset( $_POST['foo'] ) ) {
	bar( esc_html( $_POST['foo'] ) ); // Good, validated and sanitized
	bar( $_POST['foo'] ); // Error, validated but not sanitized
	bar( esc_html( $_POST['foo'] ) ); // Good, validated and sanitized
}

// Should all be OK
$empty = (
	empty( $_GET['foo'] )
	||
	empty( $_REQUEST['foo'] )
	||
	empty( $_POST['foo'] )
);

function get_current_admin_post_type() {
	if ( in_array( $pagenow, $pages ) && ! isset( $_REQUEST['post_type'] ) ) {
		$current_post_type = 'post';
	} else if ( isset( $_REQUEST['post_type'] ) ) {
		$current_post_type = sanitize_key( $_REQUEST['post_type'] );
	}
}